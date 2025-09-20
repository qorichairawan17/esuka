<?php

namespace App\Http\Controllers;

use App\Helpers\LandingHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Testimoni\TestimoniModel;
use App\Models\Pengaturan\PejabatStrukturalModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LandingController extends Controller
{
    protected $infoApp, $landingHelper;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });

        $this->landingHelper = new LandingHelper();
    }

    public function index()
    {
        $pejabatStruktural  = Cache::memo()->remember('pejabatStruktural', 60, function () {
            return PejabatStrukturalModel::first();
        });

        $testimoni = Cache::memo()->remember('testimoni', 60, function () {
            return TestimoniModel::where('publish_at', '!=', null)->with('user.profile')->orderBy('created_at', 'desc')->get();
        });

        $data = [
            'title' => config('app.name') . ' - ' . config('app.author'),
            'infoApp' => $this->infoApp,
            'pejabatStruktural' => $pejabatStruktural,
            'testimoni' => $testimoni,
            'totalSuratKuasa' => $this->landingHelper->getTotalSuratKuasa(),
            'totalUser' => $this->landingHelper->getTotalUser()
        ];

        return view('landing.home', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'Tentang - ' . config('app.name'),
            'infoApp' => $this->infoApp
        ];
        return view('landing.about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Kontak - ' . config('app.name'),
            'infoApp' => $this->infoApp
        ];
        return view('landing.contact', $data);
    }

    public function signin()
    {
        $data = [
            'title' => 'Masuk - ' . config('app.name'),
            'infoApp' => $this->infoApp
        ];
        return view('auth.signin', $data);
    }

    public function signup()
    {
        $data = [
            'title' => 'Daftar - ' . config('app.name'),
            'infoApp' => $this->infoApp
        ];
        return view('auth.signup', $data);
    }

    public function verify($uuid)
    {
        try {
            $suratKuasa = RegisterSuratKuasaModel::with([
                'pendaftaran.pihak',
                'pendaftaran.user',
                'panitera',
                'approval'
            ])->where('uuid', $uuid)->firstOrFail();

            $data = [
                'title' => 'Verifikasi Surat Kuasa - ' . config('app.name'),
                'infoApp' => $this->infoApp,
                'suratKuasa' => $suratKuasa,
            ];

            return view('landing.verify-surat-kuasa', $data);
        } catch (ModelNotFoundException $e) {
            Log::warning('Verification link accessed with invalid UUID: ' . $uuid);
            return redirect()->route('app.home')->with('error', 'Link verifikasi tidak valid atau data pendaftaran tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Failed to verify Power of Attorney: ' . $e->getMessage(), ['uuid' => $uuid, 'trace' => $e->getTraceAsString()]);
            return redirect()->route('app.home')->with('error', 'Terjadi kesalahan saat memverifikasi data.');
        }
    }
}
