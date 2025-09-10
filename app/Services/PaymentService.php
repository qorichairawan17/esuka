<?php

namespace App\Services;

use App\Enum\StatusSuratKuasaEnum;
use App\Enum\TahapanSuratKuasaEnum;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Check the payment status of a power of attorney registration based on its stage and status.
     *
     * @param PendaftaranSuratKuasaModel $suratKuasa
     * @return array
     */
    public function checkPaymentStatus(PendaftaranSuratKuasaModel $suratKuasa): array
    {
        $tahapan = $suratKuasa->tahapan;
        $status = $suratKuasa->status;

        // Rule 3: Jika tahapan verifikasi dan status disetujui, pembayaran ditolak.
        if ($tahapan == TahapanSuratKuasaEnum::Verifikasi->value && $status == StatusSuratKuasaEnum::Disetujui->value) {
            $message = 'Surat kuasa ini sudah lunas dan disetujui. Tidak dapat melakukan pembayaran ulang.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.index'
            ];
        }

        // Rule 2: Jika tahapan pembayaran dan status ditolak, bisa perbarui data pembayaran.
        // Asumsi: Tahapan 'Pembayaran' adalah saat 'tahapan' = 'Pendaftaran' dan 'status' = 'Ditolak' pada pembayaran sebelumnya.
        // Atau jika ada tahapan spesifik 'Pembayaran'. Kita akan pakai kondisi yang ada.
        if ($tahapan == TahapanSuratKuasaEnum::Pendaftaran->value && $status == StatusSuratKuasaEnum::Ditolak->value) {
            $message = 'Pembayaran sebelumnya ditolak. Anda dapat mengunggah bukti pembayaran yang baru.';
            Log::info($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => true,
                'message' => $message,
                'can_update' => true
            ];
        }

        // Rule 1: Jika tahapan pendaftaran dan status null, pembayaran bisa dilakukan.
        if ($tahapan == TahapanSuratKuasaEnum::Pendaftaran->value && is_null($status)) {
            $message = 'Silahkan lanjutkan proses pembayaran.';
            Log::info($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return ['success' => true, 'message' => $message];
        }

        // Default case if no other rules match (e.g., already in verification but not approved yet)
        $message = 'Status pendaftaran saat ini tidak memungkinkan untuk pembayaran.';
        Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar, 'tahapan' => $tahapan, 'status' => $status]);
        return ['success' => false, 'message' => $message, 'redirect' => 'surat-kuasa.index'];
    }
}
