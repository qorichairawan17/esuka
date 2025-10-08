<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;
use App\DataTables\LaporanSuratKuasaDataTable;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

    public function exportPdf(Request $request)
    {
        // Build query based on filters
        $query = PendaftaranSuratKuasaModel::with(['register', 'pihak']);

        // Filter by status
        if ($request->filled('status') && $request->get('status') != '') {
            $query->where('status', $request->get('status'));
        }

        // Filter by year
        if ($request->filled('tahun') && $request->get('tahun') != '') {
            $tahun = $request->get('tahun');
            $query->whereYear('tanggal_daftar', $tahun);
        }

        // Get filtered data
        $laporanData = $query->orderBy('tanggal_daftar', 'desc')->get();

        // Prepare data for PDF
        $data = [
            'title' => 'Laporan Pendaftaran Surat Kuasa',
            'infoApp' => $this->infoApp,
            'laporanData' => $laporanData,
            'filterStatus' => $request->get('status'),
            'filterTahun' => $request->get('tahun'),
            'tanggalCetak' => Carbon::now()->isoFormat('D MMMM Y HH:mm:ss')
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.template.pdf-laporan-surat-kuasa', $data);
        $pdf->setPaper('a4', 'landscape');

        // Generate filename
        $filename = 'Laporan-Surat-Kuasa-' . date('YmdHis') . '.pdf';

        // Download PDF
        return $pdf->download($filename);
    }
}
