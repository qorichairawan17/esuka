<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Testimoni\TestimoniModel;
use App\Models\Pengaturan\PejabatStrukturalModel;

class LandingController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    public function index()
    {
        $pejabatStruktural  = Cache::memo()->remember('pejabatStruktural', 60, function () {
            return PejabatStrukturalModel::first();
        });

        $testimoni = Cache::memo()->remember('testimoni', 60, function () {
            return TestimoniModel::where('publish', '=', '1')->with('user.profile')->orderBy('created_at', 'desc')->get();
        });

        $data = [
            'title' => config('app.name') . ' - ' . config('app.author'),
            'infoApp' => $this->infoApp,
            'pejabatStruktural' => $pejabatStruktural,
            'testimoni' => $testimoni
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
}
