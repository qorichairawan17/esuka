<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengaturan\PembayaranPnbpModel;
use App\Http\Requests\Pengaturan\AplikasiRequest;
use App\Models\Pengaturan\PejabatStrukturalModel;
use App\Http\Requests\Pengaturan\PembayaranPnbpRequest;
use App\Http\Requests\Pengaturan\PejabatStrukturalRequest;

class AplikasiController extends Controller
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
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Aplikasi', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Aplikasi - ' . config('app.name'),
            'pageTitle' => 'Aplikasi',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return view('admin.pengaturan.aplikasi', $data);
    }

    public function storeAplikasi(AplikasiRequest $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $aplikasi = AplikasiModel::find(1);
        $oldData = $aplikasi ? $aplikasi->toArray() : [];
        $newLogoPath = null;

        DB::beginTransaction();
        try {
            if ($request->hasFile('logo')) {
                $newLogoPath = $request->file('logo')->store('logo', 'public');
                $validatedData['logo'] = $newLogoPath;
            }

            $record = AplikasiModel::updateOrCreate(['id' => 1], $validatedData);

            if ($newLogoPath && ($oldData['logo'] ?? null)) {
                Storage::disk('public')->delete($oldData['logo']);
            }

            DB::commit();

            Cache::forget('infoApp');

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            AuditTrailService::record("telah {$action} pengaturan aplikasi", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            Log::error('Gagal menyimpan pengaturan aplikasi: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function pembayaran()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pembayaran & PNBP', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $pembayaran = PembayaranPnbpModel::first();

        $data = [
            'title' => 'Pembayaran & PNBP - ' . config('app.name'),
            'pageTitle' => 'Pembayaran & PNBP',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'pembayaran' => $pembayaran
        ];

        return view('admin.pengaturan.pembayaran-pnbp', $data);
    }

    public function storePembayaran(PembayaranPnbpRequest $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $pembayaran = PembayaranPnbpModel::find(1);
        $oldData = $pembayaran ? $pembayaran->toArray() : [];
        $newLogoBankPath = null;
        $newQrisPath = null;

        DB::beginTransaction();
        try {
            $dataToUpdate = [
                'nama_bank' => $validatedData['namaBank'],
                'nomor_rekening' => $validatedData['nomorRekening'],
            ];

            if ($request->hasFile('logoBank')) {
                $newLogoBankPath = $request->file('logoBank')->store('pengaturan/pembayaran', 'public');
                $dataToUpdate['logo_bank'] = $newLogoBankPath;
            }

            if ($request->hasFile('qris')) {
                $newQrisPath = $request->file('qris')->store('pengaturan/pembayaran', 'public');
                $dataToUpdate['qris'] = $newQrisPath;
            }

            $record = PembayaranPnbpModel::updateOrCreate(['id' => 1], $dataToUpdate);

            if ($newLogoBankPath && ($oldData['logo_bank'] ?? null)) {
                Storage::disk('public')->delete($oldData['logo_bank']);
            }

            if ($newQrisPath && ($oldData['qris'] ?? null)) {
                Storage::disk('public')->delete($oldData['qris']);
            }

            DB::commit();

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            AuditTrailService::record("telah {$action} pengaturan pembayaran & PNBP", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newLogoBankPath) {
                Storage::disk('public')->delete($newLogoBankPath);
            }
            if ($newQrisPath) {
                Storage::disk('public')->delete($newQrisPath);
            }

            Log::error('Gagal menyimpan pengaturan pembayaran dan PNBP: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function pejabatStruktural()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Pejabat Struktural', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $pejabat = PejabatStrukturalModel::first();

        $data = [
            'title' => 'Pejabat Struktural - ' . config('app.name'),
            'pageTitle' => 'Pejabat Struktural',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'pejabat' => $pejabat
        ];

        return view('admin.pengaturan.pejabat-struktural', $data);
    }

    public function storePejabatStruktural(PejabatStrukturalRequest $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $pejabat = PejabatStrukturalModel::find(1);
        $oldData = $pejabat ? $pejabat->toArray() : [];

        $dataToUpdate = [
            'ketua' => $validatedData['ketua'],
            'wakil_ketua' => $validatedData['wakil_ketua'],
            'panitera' => $validatedData['panitera'],
            'sekretaris' => $validatedData['sekretaris'],
        ];

        $newlyUploadedPaths = [];

        DB::beginTransaction();
        try {
            $officials = ['ketua', 'wakil_ketua', 'panitera', 'sekretaris'];
            foreach ($officials as $official) {
                $fileKey = 'foto_' . $official;
                if ($request->hasFile($fileKey)) {
                    $path = $request->file($fileKey)->store('pengaturan/pejabat', 'public');
                    $dataToUpdate[$fileKey] = $path;
                    $newlyUploadedPaths[$fileKey] = $path;
                }
            }

            $record = PejabatStrukturalModel::updateOrCreate(['id' => 1], $dataToUpdate);

            foreach ($newlyUploadedPaths as $key => $path) {
                if (isset($oldData[$key]) && $oldData[$key]) {
                    Storage::disk('public')->delete($oldData[$key]);
                }
            }

            DB::commit();

            $action = $record->wasRecentlyCreated ? 'menambahkan' : 'memperbarui';
            $message = $record->wasRecentlyCreated ? 'Data berhasil disimpan.' : 'Data berhasil diubah.';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            $context = [
                'old' => $oldData,
                'new' => $record->toArray(),
            ];
            AuditTrailService::record("telah {$action} pengaturan pejabat struktural", $context);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($newlyUploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Gagal menyimpan data pejabat struktural: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
