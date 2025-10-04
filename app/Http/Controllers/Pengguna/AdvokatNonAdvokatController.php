<?php

namespace App\Http\Controllers\Pengguna;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DataTables\AdvokatDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengaturan\AplikasiModel;
use App\Services\AdvokatNonAdvokatService;
use App\Http\Requests\Pengguna\AdvokatRequest;

class AdvokatNonAdvokatController extends Controller
{
    protected $infoApp, $advokatNonAdvokatService;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });

        $this->advokatNonAdvokatService = new AdvokatNonAdvokatService();
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Pengguna', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }

    public function index(AdvokatDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('advokat.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/Non Advokat', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Adovkat/ Non Advokat - ' . config('app.name'),
            'pageTitle' => 'Adovkat/ Non Advokat',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengguna.advokat-non-advokat.data-advokat-non-advokat', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        if ($param == 'add') {
            $title = 'Tambah Advokat/ Non Advokat';
        } else {
            if (!$user) {
                return redirect()->route('advokat.index')->with('error', 'Data Administrator tidak ditemukan.');
            }
            $title = 'Edit Advokat/ Non Advokat';
        }

        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/ Non Advokat', 'url' => route('advokat.index'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => $title, 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $id = $request->id ? Crypt::decrypt($request->id) : null;

        $data = [
            'title' => $title . ' - ' . config('app.name'),
            'pageTitle' => $title,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
        ];

        return view('admin.pengguna.advokat-non-advokat.form-advokat-non-advokat', $data);
    }

    public function store(AdvokatRequest $request): JsonResponse
    {
        return $this->advokatNonAdvokatService->store($request);
    }

    public function destroy($id): JsonResponse
    {
        return $this->advokatNonAdvokatService->destroy($id);
    }

    public function detail(Request $request)
    {
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        $breadCumb = $this->breadCumb(['url' => route('advokat.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/Non Advokat', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $breadCumb[] =  ['title' => 'Detail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Detail - ' . config('app.name'),
            'pageTitle' => 'Detail Advokat/Non Advokat',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
            'detailTitle' => $user->name,
        ];

        return view('admin.pengguna.advokat-non-advokat.detail-advokat-non-advokat', $data);
    }
}
