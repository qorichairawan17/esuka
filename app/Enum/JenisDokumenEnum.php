<?php

namespace App\Enum;

enum JenisDokumenEnum: string
{
    case KTP = 'edoc_kartu_tanda_penduduk';
    case KTA = 'edoc_kartu_tanda_anggota';
    case KTTP = 'edoc_kartu_tanda_pegawai';
    case BAS = 'edoc_berita_acara_sumpah';
    case ST = 'edoc_surat_tugas';
    case SK = 'edoc_surat_kuasa';

    /**
     * Attempt to get an enum case by its name (key).
     *
     * @param string $key The name of the enum case (e.g., 'KTP').
     * @return self|null The enum case if found, otherwise null.
     */
    public static function tryFromKey(string $key): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $key) return $case;
        }
        return null;
    }
}
