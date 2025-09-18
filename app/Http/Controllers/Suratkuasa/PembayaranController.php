<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;
use App\Enum\TahapanSuratKuasaEnum;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengaturan\PembayaranPnbpModel;
use App\Http\Requests\SuratKuasa\PembayaranRequest;
use App\Models\Suratkuasa\PembayaranSuratKuasaModel;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class PembayaranController extends Controller
{
    protected $infoApp;
    public function __construct(protected PaymentService $paymentService)
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    protected function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Surat Kuasa', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }

    public function index(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
            $suratKuasa = PendaftaranSuratKuasaModel::find($id);

            if (!$suratKuasa) {
                return redirect()->route('surat-kuasa.index')->with('error', 'Surat kuasa tidak ditemukan.');
            }

            // Check payment status using the service
            $statusCheck = $this->paymentService->checkPaymentStatus($suratKuasa);

            if (!$statusCheck['success']) {
                // Jika redirect tidak diset, kembali ke halaman sebelumnya.
                $redirectRoute = $statusCheck['redirect'] ?? 'surat-kuasa.index';
                if ($redirectRoute === 'surat-kuasa.detail') {
                    return redirect()->route($redirectRoute, ['id' => Crypt::encrypt($suratKuasa->id)])->with('error', $statusCheck['message']);
                }
                return redirect()->route($redirectRoute)->with('error', $statusCheck['message']);
            }

            $config = PembayaranPnbpModel::first();
            if (!$config) {
                return redirect()->route('surat-kuasa.index')->with('error', 'Pengaturan pembayaran tidak ditemukan. Silahkan hubungi pihak pengadilan');
            }

            $breadCumb = $this->breadCumb(['url' => route('surat-kuasa.index'), 'active' => '', 'aria' => '']);
            $breadCumb[] =  ['title' => 'Pembayaran', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

            $data = [
                'title' => 'Pembayaran Surat Kuasa - ' . config('app.name'),
                'pageTitle' => 'Pembayaran Surat Kuasa',
                'breadCumb' => $breadCumb,
                'infoApp' => $this->infoApp,
                'config' => $config,
                'suratKuasa' => $suratKuasa,
            ];

            return view('admin.surat-kuasa.pembayaran-surat-kuasa', $data);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Error decrypting payment ID: ' . $e->getMessage());
            return redirect()->route('surat-kuasa.index')->with('error', 'ID Pendaftaran tidak valid.');
        }
    }

    public function store(PembayaranRequest $request)
    {
        // Validation is now handled by PembayaranRequest.
        // We can directly use the validated data.
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $suratKuasa = PendaftaranSuratKuasaModel::findOrFail(Crypt::decrypt($id));

            // 1. Capture old data for audit trail and find existing payment to delete the old file
            $pembayaran = PembayaranSuratKuasaModel::where('surat_kuasa_id', $suratKuasa->id)->first();
            $oldData = $pembayaran ? $pembayaran->only(['jenis_pembayaran', 'bukti_pembayaran']) : [];

            // If an old payment proof exists, delete it
            if ($pembayaran && $pembayaran->bukti_pembayaran && Storage::disk('local')->exists($pembayaran->bukti_pembayaran)) {
                Storage::disk('local')->delete($pembayaran->bukti_pembayaran);
            }

            // Store the new file
            $uploadPath = 'pembayaran/' . date('m') . '/' . date('Y') . '/' . $suratKuasa->id_daftar;
            $file = $request->file('bukti_pembayaran');
            $fileName = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = "{$uploadPath}/{$fileName}";

            // Encrypt content and store
            Storage::disk('local')->put($filePath, Crypt::encryptString($file->get()));

            // 2. Capture new data for audit trail
            $newData = [
                'jenis_pembayaran' => $validated['jenis_pembayaran'],
                'bukti_pembayaran' => $filePath,
            ];

            // Create or update the payment data with the new file
            PembayaranSuratKuasaModel::updateOrCreate(
                ['surat_kuasa_id' => $suratKuasa->id],
                [
                    'tanggal_pembayaran' => date('Y-m-d'),
                    'jenis_pembayaran' => $newData['jenis_pembayaran'],
                    'bukti_pembayaran' => $newData['bukti_pembayaran'],
                    'user_payment_id' => Auth::id()
                ]
            );

            $nextTahapan = $suratKuasa->tahapan === TahapanSuratKuasaEnum::PerbaikanPembayaran->value
                ? TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value
                : TahapanSuratKuasaEnum::Pembayaran->value;

            $suratKuasa->update(['tahapan' => $nextTahapan, 'status' => null]);

            DB::commit();

            // 3. Record detailed audit trail
            $context = [
                'old' => $oldData,
                'new' => $newData,
            ];
            AuditTrailService::record('telah mengunggah bukti pembayaran untuk pendaftaran ' . $suratKuasa->id_daftar, $context);

            // 4. Send notification to admins
            if ($nextTahapan === TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value) {
                $title = 'Perbaikan Pembayaran';
                $message = "Pengguna mengajukan perbaikan pembayaran untuk ID {$suratKuasa->id_daftar}.";
            } else {
                $title = 'Pembayaran Baru';
                $message = "Pembayaran untuk ID {$suratKuasa->id_daftar} telah diunggah.";
            }
            NotificationHelper::sendToAdmins($suratKuasa, $title, $message);

            Log::info('Payment proof uploaded successfully for: ' . $suratKuasa->id_daftar);
            return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil diunggah. Pendaftaran akan segera diverifikasi.']);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($filePath) && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }
            Log::error('Failed to store payment proof: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function preview(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
            $pembayaran = PembayaranSuratKuasaModel::where('surat_kuasa_id', '=', $id)->first();

            if (!$pembayaran) {
                return abort(404, 'Data pembayaran tidak ditemukan.');
            }

            $filePath = $pembayaran->bukti_pembayaran;

            if (!$filePath || !Storage::disk('local')->exists($filePath)) {
                Log::error('Preview File payment not found: ' . $filePath, ['pembayaran_id' => $id]);
                return abort(404, 'File bukti pembayaran tidak ditemukan atau path tidak valid.');
            }

            // Get encrypted content, decrypt it, and then create a response
            $encryptedContent = Storage::disk('local')->get($filePath);
            $decryptedContent = Crypt::decryptString($encryptedContent);

            return response($decryptedContent)->header('Content-Type', Storage::mimeType($filePath));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Error decrypting payment preview ID: ' . $e->getMessage());
            return abort(404, 'ID pembayaran tidak valid.');
        }
    }
}
