<?php

namespace App\Enum;

enum TahapanSuratKuasaEnum: string
{
    case Pendaftaran = 'Pendaftaran';
    case Pembayaran = 'Pembayaran';
    case PerbaikanData = 'Perbaikan Data';
    case PerbaikanPembayaran = 'Perbaikan Pembayaran';
    case Verifikasi = 'Verifikasi';
}
