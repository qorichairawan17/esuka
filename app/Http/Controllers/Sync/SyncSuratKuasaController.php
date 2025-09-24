<?php

namespace App\Http\Controllers\Sync;

use App\DataTables\StagingSuratKuasaDataTable;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Sync\StagingSyncSuratKuasaModel;
use App\Models\Pengaturan\AplikasiModel;

class SyncSuratKuasaController extends Controller
{
    private $infoApp, $syncService;

    public function __construct()
    {
        $this->syncService = new SyncService();

        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
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

    public function index(StagingSuratKuasaDataTable $datatable)
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.admin'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Staging Synchronize', 'url' => route('sync.index'), 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Staging Synchronize - ' . config('app.name'),
            'pageTitle' => 'Staging Synchronize',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
        ];

        return $datatable->render('admin.sync.data-sync', $data);
    }

    public function show($id): JsonResponse
    {
        return $this->syncService->fetchShow($id);
    }

    public function fetchDataOnDB(Request $request): JsonResponse
    {
        return $this->syncService->fetchData($request->klasifikasi);
    }

    public function fetchEdoc(Request $request)
    {
        return $this->syncService->fetchEdoc($request->klasifikasi);
    }

    public function destroy()
    {
        return $this->syncService->fetchDelete();
    }
}
