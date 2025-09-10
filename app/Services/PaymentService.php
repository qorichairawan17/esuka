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

        // Rule: Jika tahapan 'Perbaikan Data', pembayaran ditolak.
        if ($tahapan == TahapanSuratKuasaEnum::PerbaikanData->value) {
            $message = 'Pendaftaran Anda memerlukan perbaikan data, bukan pembayaran. Silakan perbaiki data pendaftaran.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.detail'
            ];
        }

            // Rule: Jika tahapan 'Pengajuan Perbaikan', pembayaran ditolak karena menunggu verifikasi.
        if (in_array($tahapan, [TahapanSuratKuasaEnum::PengajuanPerbaikanData->value, TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value])) {
            $message = 'Pendaftaran Anda sedang dalam proses verifikasi oleh petugas. Tidak dapat melakukan pembayaran saat ini.';
            Log::warning($message, ['id_daftar' => $suratKuasa->id_daftar]);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => 'surat-kuasa.detail'
            ];
        }

        // Rule: Jika tahapan 'Perbaikan Pembayaran', pembayaran diizinkan.
        if ($tahapan == TahapanSuratKuasaEnum::PerbaikanPembayaran->value) {
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
