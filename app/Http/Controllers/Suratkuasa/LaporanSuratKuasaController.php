<?php

namespace App\Http\Controllers\Suratkuasa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;
use App\DataTables\LaporanSuratKuasaDataTable;

class LaporanSuratKuasaController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Surat Kuasa', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index(LaporanSuratKuasaDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('surat-kuasa.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Laporan', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Laporan Surat Kuasa - ' . config('app.name'),
            'pageTitle' => 'Laporan Surat Kuasa',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.surat-kuasa.laporan', $data);
    }
}
