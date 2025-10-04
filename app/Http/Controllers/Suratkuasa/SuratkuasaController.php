<?php

namespace App\Http\Controllers\Suratkuasa;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enum\TahapanSuratKuasaEnum;
use App\Services\SuratKuasaService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use App\Models\Pengaturan\AplikasiModel;
use App\DataTables\PendaftaranSuratKuasaDataTable;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class SuratkuasaController extends Controller
{
    protected $infoApp;

    public function __construct(protected SuratKuasaService $suratKuasaService)
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
    public function index(PendaftaranSuratKuasaDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('surat-kuasa.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pendaftaran', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Pendaftaran Surat Kuasa - ' . config('app.name'),
            'pageTitle' => 'Pendaftaran Surat Kuasa',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.surat-kuasa.data-surat-kuasa', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $klasifikasi = $request->klasifikasi;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $suratKuasa = $id ? PendaftaranSuratKuasaModel::find($id) : null;

        if ($param == 'add') {
            $pageTitle = 'Pendaftaran Surat Kuasa';
            $idDaftar = '#' . $this->infoApp->kode_dipa . '-' . Str::upper(Str::random(3)) . Str::numbers(3);
        } else {
            if (!$suratKuasa) {
                return redirect()->route('surat-kuasa.index')->with('error', 'Data surat kuasa tidak ditemukan.');
            }
            if ($suratKuasa->tahapan !== TahapanSuratKuasaEnum::PerbaikanData->value) {
                return redirect()->route('surat-kuasa.detail', ['id' => $request->id])->with('error', 'Surat kuasa ini tidak dalam tahap perbaikan data.');
            }

            $pageTitle = 'Edit Surat Kuasa';
            $idDaftar = $suratKuasa->id_daftar;
        }

        session()->forget('klasifikasi');
        session()->put('klasifikasi', $klasifikasi);

        $breadCumb = $this->breadCumb(['url' => route('surat-kuasa.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pendaftaran', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Pendaftaran Surat Kuasa - ' . config('app.name'),
            'pageTitle' => $pageTitle,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'suratKuasa' => $suratKuasa,
            'idDaftar' => $idDaftar
        ];

        return view('admin.surat-kuasa.form-daftar-surat-kuasa', $data);
    }

    public function detail(Request $request)
    {
        $breadCumb = $this->breadCumb(['url' => route('surat-kuasa.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pendaftaran', 'url' => route('surat-kuasa.detail'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => 'Detail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $suratKuasa = PendaftaranSuratKuasaModel::with(['user.profile', 'pihak', 'register.approval', 'register.panitera', 'pembayaran'])->find(Crypt::decrypt($request->id));

        $panitera = PaniteraModel::where('aktif', 1)->get();
        if (!$suratKuasa) {
            return redirect()->back()->with('error', 'Surat Kuasa tidak ditemukan.');
        }

        // Generate the proposed number to be shown in the approval modal
        $nomorSuratKuasaBaru = $this->suratKuasaService->generateNomorSuratKuasa();

        $data = [
            'title' => 'Surat Kuasa ' . $suratKuasa->pemohon . ' - ' . config('app.name'),
            'pageTitle' => 'Detail Pendaftaran Surat Kuasa',
            'breadCumb' => $breadCumb,
            'suratKuasa' => $suratKuasa,
            'infoApp' => $this->infoApp,
            'panitera' => $panitera,
            'nomorSuratKuasaBaru' => $nomorSuratKuasaBaru
        ];

        return view('admin.surat-kuasa.detail-surat-kuasa', $data);
    }

    public function downloadFile(Request $request)
    {
        return $this->suratKuasaService->downloadFile($request);
    }

    public function previewFile(Request $request)
    {
        return $this->suratKuasaService->previewFile($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->suratKuasaService->store($request);
    }

    public function update(Request $request, $id): JsonResponse
    {
        return $this->suratKuasaService->update($request, $id);
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->suratKuasaService->destroy($request);
    }

    public function destroyRejected(): JsonResponse
    {
        return $this->suratKuasaService->destroyRejected();
    }
}
