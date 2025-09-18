<?php

namespace App\Helpers;

use App\Models\AuditTrail\AuditTrailModel;
use Illuminate\Support\Facades\Cache;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Models\Testimoni\TestimoniModel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class HomeHelper
{
    public function userTotal()
    {
        $userCount = User::where('role', '=', \App\Enum\RoleEnum::User->value)->count();
        return $userCount;
    }

    public function suratKuasaTotal()
    {
        $suratKuasaCount = PendaftaranSuratKuasaModel::where('status', '=', \App\Enum\StatusSuratKuasaEnum::Disetujui->value)->count();
        return $suratKuasaCount;
    }

    public function testimoniTotal()
    {
        $testimoniCount = TestimoniModel::all()->count();
        return $testimoniCount;
    }

    public function verifikasiSuratKuasa()
    {
        $suratKuasa = PendaftaranSuratKuasaModel::where('tahapan', '!=', \App\Enum\TahapanSuratKuasaEnum::Verifikasi->value)
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();

        return $suratKuasa;
    }

    public function statusSuratKuasa($param)
    {
        $suratKuasa = PendaftaranSuratKuasaModel::where('status', '=', $param)->whereYear('created_at', date('Y'))->count();
        return $suratKuasa;
    }

    public function tahapanSuratKuasa($param)
    {
        $suratKuasa = PendaftaranSuratKuasaModel::where('tahapan', '=', $param)->whereYear('created_at', date('Y'))->count();
        return $suratKuasa;
    }

    /**
     * Get monthly data for approved power of attorney registrations for the current year.
     *
     * @return array
     */
    public function getChart(): array
    {
        $currentYear = date('Y');
        $cacheKey = "chart_data_{$currentYear}";

        // Cache the data for 1 day (1440 minutes) or until invalidated.
        return Cache::remember($cacheKey, 1440, function () use ($currentYear) {
            $monthlyCounts = array_fill(0, 12, 0);

            $results = PendaftaranSuratKuasaModel::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
                ->where('status', \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->get();

            foreach ($results as $result) {
                // Ensure the index is not out of bounds if the month is 0.
                if ($result->month > 0) {
                    $monthlyCounts[$result->month - 1] = $result->count;
                }
            }

            return $monthlyCounts;
        });
    }

    public function lastAuditTrail()
    {
        $auditTrail = AuditTrailModel::with('user')->orderBy('created_at', 'desc')->first();
        return $auditTrail;
    }

    public function getPembayaranSuratKuasa()
    {
        // Mengambil surat kuasa milik user yang sedang login,
        // yang berada di tahap 'Pendaftaran' (belum bayar) atau 'Perbaikan Pembayaran'.
        return PendaftaranSuratKuasaModel::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('tahapan', \App\Enum\TahapanSuratKuasaEnum::Pendaftaran->value)
                    ->orWhere('tahapan', \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value);
            })
            ->orderBy('created_at', 'desc')->get();
    }

    public function getTestimoniByUser()
    {
        return TestimoniModel::where('user_id', auth()->id())->first();
    }

    /**
     * Get monthly data for the logged-in user's approved power of attorney registrations for the current year.
     *
     * @return array
     */
    public function getChartForUser(): array
    {
        $userId = auth()->id();
        $currentYear = date('Y');
        $cacheKey = "chart_data_user_{$userId}_{$currentYear}";

        // Cache the data for 1 day (1440 minutes) or until invalidated.
        return Cache::remember($cacheKey, 1440, function () use ($userId, $currentYear) {
            $monthlyCounts = array_fill(0, 12, 0);

            $results = PendaftaranSuratKuasaModel::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
                ->where('user_id', $userId)
                ->where('status', \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->get();

            foreach ($results as $result) {
                if ($result->month > 0) {
                    $monthlyCounts[$result->month - 1] = $result->count;
                }
            }
            return $monthlyCounts;
        });
    }

    public function pendaftaranSuratKuasaByUser($id)
    {
        $suratKuasa = PendaftaranSuratKuasaModel::where('user_id', $id)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->orderBy('created_at', 'desc')->limit(5)->get();
        return $suratKuasa;
    }
}
