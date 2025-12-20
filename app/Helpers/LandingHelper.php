<?php

namespace App\Helpers;

use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class LandingHelper
{
    public function getTotalSuratKuasa()
    {
        $suratKuasa = Cache::remember('total_surat_kuasa', 3600, function () {
            return PendaftaranSuratKuasaModel::where('status', '=', \App\Enum\StatusSuratKuasaEnum::Disetujui->value)->count();
        });
        return $suratKuasa;
    }

    public function getTotalUser()
    {
        $user = Cache::remember('total_user', 3600, function () {
            return User::where('role', '=', \App\Enum\RoleEnum::User->value)->count();
        });
        return $user;
    }
}
