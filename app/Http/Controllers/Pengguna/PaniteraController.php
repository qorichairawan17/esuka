<?php

namespace App\Http\Controllers\Pengguna;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\PaniteraDataTable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use App\Models\Pengaturan\AplikasiModel;
use App\Http\Requests\Pengguna\PaniteraRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class PaniteraController extends Controller
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

    public function store(PaniteraRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $id = $request->filled('id') ? Crypt::decrypt($request->input('id')) : null;

        DB::beginTransaction();
        try {
            if ($id) {
                // Update existing record
                $panitera = PaniteraModel::find($id);
                if (!$panitera) {
                    return response()->json(['success' => false, 'message' => 'Data panitera tidak ditemukan.'], 404);
                }
                $panitera->update($validatedData);
                $message = 'Data panitera berhasil diubah.';
                AuditTrailService::record('memperbarui data administrator : ' . $validatedData['nama'] . ' pada ' . now()->format('d F Y, h:i A'));
            } else {
                // Create new record
                $validatedData['created_by'] = Auth::id();
                PaniteraModel::create($validatedData);
                $message = 'Data panitera berhasil ditambahkan.';
                AuditTrailService::record('menambahkan data administrator : ' . $validatedData['nama'] . ' pada ' . now()->format('d F Y, h:i A'));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving panitera: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            try {
                $decryptedId = Crypt::decrypt($id);
            } catch (DecryptException $e) {
                Log::warning('Gagal mendekripsi ID panitera untuk dihapus: ' . $id, ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }

            $panitera = PaniteraModel::find($decryptedId);

            if (!$panitera) {
                return response()->json(['success' => false, 'message' => 'Data panitera tidak ditemukan.'], 404);
            }

            AuditTrailService::record('menghapus data panitera : ' . $panitera->nama . ' pada ' . now()->format('d F Y, h:i A'));

            $panitera->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data panitera berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting panitera: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
