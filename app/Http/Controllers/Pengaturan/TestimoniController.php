<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\AuditTrailService;
use App\Http\Controllers\Controller;
use App\DataTables\TestimoniDataTable;
use Illuminate\Support\Facades\Crypt;
use App\Models\Testimoni\TestimoniModel;
use App\Models\Pengaturan\AplikasiModel;

class TestimoniController extends Controller
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
            ['title' => 'Pengaturan', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index(TestimoniDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Testimoni', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Testimoni - ' . config('app.name'),
            'pageTitle' => 'Testimoni',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengaturan.testimoni', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $existingTestimoni = TestimoniModel::where('user_id', Auth::id())->first();
            $oldData = $existingTestimoni ? $existingTestimoni->only(['rating', 'testimoni']) : [];

            $testimoni = TestimoniModel::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'rating' => $validated['rating'],
                    'testimoni' => $validated['pesan'],
                ]
            );

            $action = $testimoni->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $context = [
                'old' => $oldData,
                'new' => [
                    'rating' => $validated['rating'],
                    'testimoni' => $validated['pesan'],
                ]
            ];
            AuditTrailService::record("telah {$action} testimoni", $context);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Terima kasih, testimoni Kamu telah disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving testimoni: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan testimoni.'], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        try {
            $decryptedId = Crypt::decrypt($request->id);
            $testimoni = TestimoniModel::findOrFail($decryptedId);
            return response()->json($testimoni);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Gagal mendekripsi ID testimoni: ' . $request->id, ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan.'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string|max:500',
            'publish' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $decryptedId = Crypt::decrypt($id);
            $testimoni = TestimoniModel::with('user')->findOrFail($decryptedId);

            // Ambil data lama untuk audit trail
            $oldData = $testimoni->only(['rating', 'testimoni', 'publish_at']);

            $testimoni->update([
                'rating' => $validated['rating'],
                'testimoni' => $validated['pesan'],
                'publish_at' => isset($validated['publish']) && $validated['publish'] ? now() : null,
            ]);

            // Siapkan data baru untuk audit trail
            $newData = [
                'rating' => $validated['rating'],
                'testimoni' => $validated['pesan'],
                'publish_at' => $testimoni->publish_at ? $testimoni->publish_at->toDateTimeString() : null,
            ];

            // Catat audit trail
            $context = ['old' => $oldData, 'new' => $newData];
            AuditTrailService::record("telah memperbarui testimoni dari pengguna: " . $testimoni->user->name, $context);

            DB::commit();

            Log::info('Testimoni berhasil diperbarui', ['id' => $decryptedId, 'user_id' => $testimoni->user_id]);

            return response()->json(['success' => true, 'message' => 'Testimoni berhasil diperbarui.']);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'ID testimoni tidak valid.'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating testimoni: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
