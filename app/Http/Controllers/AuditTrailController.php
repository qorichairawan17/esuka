<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\AuditTrailDataTable;
use App\Models\AuditTrail\AuditTrailModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuditTrailController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return \App\Models\Pengaturan\AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $dashboardRoute = Auth::user()->role === 'User' ? route('dashboard.pengguna') : route('dashboard.admin');
        $breadCumb = [
            ['title' => 'Dashboard', 'url' => $dashboardRoute, 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index(AuditTrailDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Audit Trail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Audit Trail - ' . config('app.name'),
            'pageTitle' => 'Audit Trail',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.audit-trail.data-audit-trail', $data);
    }

    /**
     * Show detail data audit trail.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $query = AuditTrailModel::with('user:id,name');
            if (Auth::user()->role === 'User') {
                $query->where('user_id', Auth::id());
            }
            $audit = $query->findOrFail($id);
            return response()->json($audit);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }
    }
}
