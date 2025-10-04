<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Enum\StatusSuratKuasaEnum;
use Illuminate\Support\Facades\DB;
use App\Enum\TahapanSuratKuasaEnum;
use App\Helpers\NotificationHelper;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\Suratkuasa\PembayaranSuratKuasaModel;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

/**
 * Class PaymentService
 *
 * This service handles the business logic related to payments for "Surat Kuasa" (Power of Attorney).
 * It includes checking payment status, storing payment proofs, and providing previews of payment documents.
 */
class PaymentService
{
    /**
     * Check the payment status of a power of attorney registration based on its stage and status.
     *
     * This method evaluates the current stage and status of a "Surat Kuasa" to determine
     * if a payment can be made, needs correction, or is already completed.
     *
     * @param PendaftaranSuratKuasaModel $suratKuasa The power of attorney registration model.
     * @return array An array containing the status check result, a message, and an optional redirect route.
     */
    public function checkPaymentStatus(PendaftaranSuratKuasaModel $suratKuasa): array
    {
        $tahapan = $suratKuasa->tahapan;
        $status = $suratKuasa->status;

        if ($tahapan == TahapanSuratKuasaEnum::Verifikasi->value && $status == StatusSuratKuasaEnum::Disetujui->value) {
            $message = 'Surat kuasa ini sudah lunas dan disetujui. Tidak dapat melakukan pembayaran ulang.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.index'
            ];
        }

        if ($tahapan == TahapanSuratKuasaEnum::PerbaikanData->value) {
            $message = 'Pendaftaran kamu memerlukan perbaikan data, bukan pembayaran. Silakan perbaiki data pendaftaran.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.detail'
            ];
        }

        if (in_array($tahapan, [TahapanSuratKuasaEnum::PengajuanPerbaikanData->value, TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value])) {
            $message = 'Pendaftaran kamu sedang dalam proses verifikasi oleh petugas. Tidak dapat melakukan pembayaran saat ini.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.detail'
            ];
        }

        if ($tahapan == TahapanSuratKuasaEnum::PerbaikanPembayaran->value) {
            $message = 'Pembayaran sebelumnya ditolak. Kamu dapat mengunggah bukti pembayaran yang baru.';
            Log::info($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => true,
                'message' => $message,
                'can_update' => true
            ];
        }

        if ($tahapan == TahapanSuratKuasaEnum::Pendaftaran->value && is_null($status)) {
            $message = 'Silakan lanjutkan proses pembayaran.';
            Log::info($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return ['success' => true, 'message' => $message];
        }

        $message = 'Status pendaftaran saat ini tidak memungkinkan untuk pembayaran.';
        Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar, 'tahapan' => $tahapan, 'status' => $status]);
        return ['success' => false, 'message' => $message, 'redirect' => 'surat-kuasa.index'];
    }

    /**
     * Store the payment proof for a power of attorney registration.
     *
     * This method validates the request, handles the file upload of the payment proof,
     * encrypts and stores the file, updates the database records, and sends notifications.
     * It also records an audit trail for the action.
     *
     * @param \Illuminate\Http\Request $request The request object containing payment data and the uploaded file.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function storePayment($request)
    {
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
            $uploadPath = 'pembayaran/' . date('Y') . '/' . date('m') . '/' . $suratKuasa->id_daftar;
            $file = $request->file('bukti_pembayaran');
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
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

    /**
     * Preview the uploaded payment proof.
     *
     * This method retrieves the encrypted payment proof from storage, decrypts it,
     * and returns it as a file response to be displayed in the browser.
     *
     * @param \Illuminate\Http\Request $request The request object containing the encrypted ID of the payment.
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function preview($request)
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
