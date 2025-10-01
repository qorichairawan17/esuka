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
}
