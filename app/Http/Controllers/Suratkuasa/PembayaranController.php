<?php

namespace App\Http\Controllers\Suratkuasa;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengaturan\PembayaranPnbpModel;
use App\Http\Requests\SuratKuasa\PembayaranRequest;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class PembayaranController extends Controller
{
    protected $infoApp;

    public function __construct(protected PaymentService $paymentService)
    {
        $this->infoApp = Cache::remember('infoApp', 3600, function () {
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
        return $this->paymentService->storePayment($request);
    }

    public function preview(Request $request)
    {
        return $this->paymentService->preview($request);
    }
}
