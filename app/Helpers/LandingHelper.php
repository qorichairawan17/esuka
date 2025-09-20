<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class LandingHelper
{
    public function getTotalSuratKuasa()
    {
        return PendaftaranSuratKuasaModel::where('status', '=', \App\Enum\StatusSuratKuasaEnum::Disetujui->value)->count();
    }

    public function getTotalUser()
    {
        return User::where('role', '=', \App\Enum\RoleEnum::User->value)->count();
    }
}
