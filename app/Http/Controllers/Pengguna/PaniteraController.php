<?php

namespace App\Http\Controllers\Pengguna;

use Illuminate\Http\Request;
use App\Services\PaniteraService;
use App\Http\Controllers\Controller;
use App\DataTables\PaniteraDataTable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use App\Models\Pengaturan\AplikasiModel;
use App\Http\Requests\Pengguna\PaniteraRequest;

class PaniteraController extends Controller
{
    protected $infoApp;
    public function __construct(protected PaniteraService $paniteraService)
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Pengguna', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }

    public function index(PaniteraDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('panitera.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Panitera', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Panitera - ' . config('app.name'),
            'pageTitle' => 'Panitera',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengguna.panitera.data-panitera', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $panitera = $id ? PaniteraModel::find($id) : null;

        if ($param == 'add') {
            $title = 'Tambah Panitera';
        } else {
            if (!$panitera) {
                return redirect()->route('panitera.index')->with('error', 'Data Panitera tidak ditemukan.');
            }
            $title = 'Edit Panitera';
        }

        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Panitera', 'url' => route('panitera.index'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => $title, 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => $title . ' - ' . config('app.name'),
            'pageTitle' => $title,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'panitera' => $panitera,
        ];

        return view('admin.pengguna.panitera.form-panitera', $data);
    }

    public function store(PaniteraRequest $request)
    {
        return $this->paniteraService->store($request);
    }

    public function destroy($id)
    {
        return $this->paniteraService->destroy($id);
    }
}
