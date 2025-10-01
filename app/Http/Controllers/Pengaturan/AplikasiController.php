<?php

namespace App\Http\Controllers\Pengaturan;

use App\Services\AplikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengaturan\PembayaranPnbpModel;
use App\Http\Requests\Pengaturan\AplikasiRequest;
use App\Models\Pengaturan\PejabatStrukturalModel;
use App\Http\Requests\Pengaturan\PembayaranPnbpRequest;
use App\Http\Requests\Pengaturan\PejabatStrukturalRequest;

class AplikasiController extends Controller
{
    protected $infoApp, $aplikasiService;
    public function __construct()
    {
        $this->aplikasiService = new AplikasiService();
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Pengaturan', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Aplikasi', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Aplikasi - ' . config('app.name'),
            'pageTitle' => 'Aplikasi',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return view('admin.pengaturan.aplikasi', $data);
    }

    public function storeAplikasi(AplikasiRequest $request)
    {
        $request->validated();
        return $this->aplikasiService->storeApp($request);
    }

    public function pembayaran()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pembayaran & PNBP', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $pembayaran = PembayaranPnbpModel::first();

        $data = [
            'title' => 'Pembayaran & PNBP - ' . config('app.name'),
            'pageTitle' => 'Pembayaran & PNBP',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'pembayaran' => $pembayaran
        ];

        return view('admin.pengaturan.pembayaran-pnbp', $data);
    }

    public function storePembayaran(PembayaranPnbpRequest $request)
    {
        return $this->aplikasiService->storePayment($request);
    }

    public function pejabatStruktural()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pejabat Struktural', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $pejabat = PejabatStrukturalModel::first();

        $data = [
            'title' => 'Pejabat Struktural - ' . config('app.name'),
            'pageTitle' => 'Pejabat Struktural',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'pejabat' => $pejabat
        ];

        return view('admin.pengaturan.pejabat-struktural', $data);
    }

    public function storePejabatStruktural(PejabatStrukturalRequest $request)
    {
        return $this->aplikasiService->storePejabatStruktural($request);
    }
}
