<?php

namespace App\Http\Controllers\Pengguna;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Services\AdministratorService;
use App\Models\Pengaturan\AplikasiModel;
use App\DataTables\AdministratorDataTable;
use App\Http\Requests\Pengguna\AdministratorRequest;

class AdministratorController extends Controller
{
    protected $infoApp;

    public function __construct(protected AdministratorService $administratorService)
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

    public function index(AdministratorDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Administrator - ' . config('app.name'),
            'pageTitle' => 'Administrator',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengguna.administrator.data-administrator', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        if ($param == 'add') {
            $title = 'Tambah Administrator';
        } else {
            if (!$user) {
                return redirect()->route('administrator.index')->with('error', 'Data Administrator tidak ditemukan.');
            }
            $title = 'Edit Administrator';
        }

        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => route('administrator.index'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => $title, 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => $title . ' - ' . config('app.name'),
            'pageTitle' => $title,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
        ];

        return view('admin.pengguna.administrator.form-administrator', $data);
    }

    public function store(AdministratorRequest $request): JsonResponse
    {
        return $this->administratorService->store($request);
    }

    public function destroy($id): JsonResponse
    {
        return $this->administratorService->destroy($id);
    }

    public function detail(Request $request)
    {
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $breadCumb[] =  ['title' => 'Detail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Detail - ' . config('app.name'),
            'pageTitle' => 'Detail Administrator',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
            'detailTitle' => $user->name,
        ];

        return view('admin.pengguna.administrator.detail-administrator', $data);
    }
}
