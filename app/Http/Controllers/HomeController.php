<?php

namespace App\Http\Controllers;

use App\Helpers\HomeHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;

class HomeController extends Controller
{
    protected $infoApp;
    public function __construct(protected HomeHelper $homeHelper)
    {
        $this->infoApp = Cache::remember('infoApp', 3600, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Dashboard', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.admin'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Home', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Home - ' . config('app.name'),
            'pageTitle' => config('app.name'),
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'userTotal' => $this->homeHelper->userTotal(),
            'suratKuasaTotal' => $this->homeHelper->suratKuasaTotal(),
            'testimoniTotal' => $this->homeHelper->testimoniTotal(),
            'verifikasiSuratKuasa' => $this->homeHelper->verifikasiSuratKuasa(),
            'statusSuratKuasa' => [
                'disetujui' => $this->homeHelper->statusSuratKuasa(\App\Enum\StatusSuratKuasaEnum::Disetujui->value),
                'ditolak' => $this->homeHelper->statusSuratKuasa(\App\Enum\StatusSuratKuasaEnum::Ditolak->value),
            ],
            'tahapanSuratKuasa' => [
                'pendaftaran' => $this->homeHelper->tahapanSuratKuasa(\App\Enum\TahapanSuratKuasaEnum::Pendaftaran->value),
                'pembayaran' => $this->homeHelper->tahapanSuratKuasa(\App\Enum\TahapanSuratKuasaEnum::Pembayaran->value),
            ],
            'lastAuditTrail' => $this->homeHelper->lastAuditTrail(),
            'chartData' => $this->homeHelper->getChart()
        ];

        return view('admin.home.home-admin', $data);
    }

    public function pengguna()
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.pengguna'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Home', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Home - ' . config('app.name'),
            'pageTitle' => config('app.name'),
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'pembayaranSuratKuasa' => $this->homeHelper->getPembayaranSuratKuasa(),
            'pendaftaranSuratKuasa' => $this->homeHelper->pendaftaranSuratKuasaByUser(Auth::user()->id),
            'testimoniUser' => $this->homeHelper->getTestimoniByUser(),
            'chartData' => $this->homeHelper->getChartForUser()
        ];

        return view('admin.home.home-pengguna', $data);
    }
}
