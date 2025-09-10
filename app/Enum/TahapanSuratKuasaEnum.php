<?php

namespace App\Enum;

enum TahapanSuratKuasaEnum: string
{
    case Pendaftaran = 'Pendaftaran';
    case Pembayaran = 'Pembayaran';
    case PerbaikanData = 'Perbaikan Data';
    case PengajuanPerbaikanData = 'Pengajuan Perbaikan Data';
    case PerbaikanPembayaran = 'Perbaikan Pembayaran';
    case PengajuanPerbaikanPembayaran = 'Pengajuan Perbaikan Pembayaran';
    case Verifikasi = 'Verifikasi';
}
