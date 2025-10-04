<?php

namespace App\Http\Controllers\Sync;

use App\DataTables\StagingSuratKuasaDataTable;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;

class SyncSuratKuasaController extends Controller
{
    private $infoApp;

    public function __construct(protected SyncService $syncService)
    {
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

    public function show(Request $request): JsonResponse
    {
        if (!$request->route('id')) {
            return response()->json(['message' => 'ID parameter is missing.'], 400);
        }
        return $this->syncService->fetchShow($request->route('id'));
    }

    public function fetchDataOnDB(Request $request): JsonResponse
    {
        return $this->syncService->fetchData($request->klasifikasi);
    }

    public function destroy()
    {
        return $this->syncService->fetchDelete();
    }

    public function migrate(): JsonResponse
    {
        try {
            $response = $this->syncService->migrateStagingData();
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error during bulk staging data migration: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server saat melakukan migrasi data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
