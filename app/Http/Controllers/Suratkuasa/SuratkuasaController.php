<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Enum\SuratKuasaEnum;
use Illuminate\Http\Request;
use App\Enum\PihakSuratKuasaEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enum\TahapanSuratKuasaEnum;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Suratkuasa\PihakSuratKuasaModel;
use App\DataTables\PendaftaranSuratKuasaDataTable;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use App\Http\Requests\SuratKuasa\SuratKuasaAdvokatRequest;
use App\Http\Requests\SuratKuasa\SuratKuasaNonAdvokatRequest;

class SuratkuasaController extends Controller
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
            $pageTitle = 'Edit Surat Kuasa';
            $idDaftar = $suratKuasa->id_daftar;
        }

        // Store klasifikasi surat kuasa in session
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

    public function generateNomorSuratKuasa()
    {
        // Get the format from application settings
        $format = $this->infoApp->kode_surat_kuasa;
        // Example: #NOMOR/W2-U4/SK/#BULAN/#TAHUN/PN Lbp

        $now = Carbon::now();
        $currentYear = $now->year;

        // Find the latest record for the current year to handle annual reset.
        // Ordering by 'id' is more reliable than timestamp for the absolute latest record.
        $latestRegister = RegisterSuratKuasaModel::whereYear('created_at', $currentYear)
            ->orderBy('created_at', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latestRegister) {
            // This parsing is fragile and depends on the number being the first segment.
            // A dedicated 'sequence_number' column in the database is highly recommended for robustness.
            $parts = explode('/', $latestRegister->nomor_surat_kuasa);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $nextNumber = (int)$parts[0] + 1;
            }
        }

        // Prepare replacements in a single array for efficiency and readability
        $placeholders = ['#NOMOR', '#BULAN', '#TAHUN'];
        $replacements = [
            $nextNumber,
            $now->format('m'),
            $currentYear
        ];

        return str_replace($placeholders, $replacements, $format);
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
        $nomorSuratKuasaBaru = $this->generateNomorSuratKuasa();

        $data = [
            'title' => 'Detail Pendaftaran Surat Kuasa - ' . config('app.name'),
            'pageTitle' => 'Detail Pendaftaran Surat Kuasa',
            'breadCumb' => $breadCumb,
            'suratKuasa' => $suratKuasa,
            'infoApp' => $this->infoApp,
            'panitera' => $panitera,
            'nomorSuratKuasaBaru' => $nomorSuratKuasaBaru // Pass the new number to the view
        ];

        return view('admin.surat-kuasa.detail-surat-kuasa', $data);
    }

    public function downloadFile(Request $request)
    {
        try {
            $filePath = Crypt::decrypt($request->path);

            if (Storage::disk('local')->exists($filePath)) {
                return response()->file(storage_path('app/private/' . $filePath));
            }
            Log::error('File not found: ' . $filePath);
            return abort(404, 'File tidak ditemukan.');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption error
            Log::error('Error decrypting file path: ' . $e->getMessage());
            return abort(404, 'Path file tidak valid.');
        }
    }

    public function previewFile(Request $request)
    {
        try {
            // Decryptction id and jenis dokumen from request
            $id = Crypt::decrypt($request->id);
            $jenis_dokumen = $request->jenis_dokumen;

            // Find surat kuasa by id
            $suratKuasa = PendaftaranSuratKuasaModel::find($id);

            if (!$suratKuasa) {
                return abort(404, 'Data pendaftaran tidak ditemukan.');
            }

            // Find column name based on jenis dokumen
            $columnName = \App\Enum\JenisDokumenEnum::tryFromKey(strtoupper($jenis_dokumen));

            if (is_null($columnName)) {
                return abort(400, 'Jenis dokumen tidak valid.');
            }

            // Get file path
            $filePath = $suratKuasa->{$columnName->value};

            if (!$filePath || !Storage::disk('local')->exists($filePath)) {
                Log::error('Preview File not found: ' . $filePath);
                return abort(404, 'File tidak ditemukan atau path tidak valid.');
            }

            // Get extension from file path
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            // Make Friendly Url to showing
            $pemohonName = Str::slug($suratKuasa->pemohon, '_');
            $newFileName = strtoupper($jenis_dokumen) . '_' . $pemohonName . '.' . $extension;

            // Set headers custom
            $headers = [
                'Content-Disposition' => 'inline; filename="' . $newFileName . '"'
            ];

            return response()->file(storage_path('app/private/' . $filePath), $headers);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption error
            Log::error('Error decrypting preview file path: ' . $e->getMessage());
            return abort(404, 'ID pendaftaran tidak valid.');
        }
    }

    public function store(Request $request): JsonResponse
    {
        $klasifikasi = $request->input('klasifikasi');
        $idDaftar = $request->input('idDaftar');

        // Get data pihak from (JSON string) request
        $pemberiKuasaJson = $request->input('pemberi_kuasa');
        $penerimaKuasaJson = $request->input('penerima_kuasa');

        // Determine which request rules to use based on 'jenis'
        // We create a temporary request to validate against.
        $formRequest = null;
        if ($klasifikasi == SuratKuasaEnum::Advokat->value) {
            $formRequest = new SuratKuasaAdvokatRequest();
        } elseif ($klasifikasi == SuratKuasaEnum::NonAdvokat->value) {
            $formRequest = new SuratKuasaNonAdvokatRequest();
        } else {
            return response()->json(['message' => 'Jenis surat kuasa tidak valid.'], 400);
        }
        $validated = $request->validate($formRequest->rules(false), $formRequest->messages());

        DB::beginTransaction();
        try {
            // 1. Handle File Uploads
            $filePaths = [];
            $uploadPath = 'surat-kuasa/' . date('m') . '/' . date('Y') . '/' . $idDaftar;

            $fileFields = ($klasifikasi === SuratKuasaEnum::Advokat->value)
                ? ['ktp', 'kta', 'bas', 'suratKuasa']
                : ['ktp', 'ktpp', 'suratTugas', 'suratKuasa'];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $filePaths[$field] = $request->file($field)->store($uploadPath, 'local');
                }
            }

            // 2. Create Main Registration Record
            $pendaftaran = PendaftaranSuratKuasaModel::create([
                'id_daftar' => $idDaftar,
                'tanggal_daftar' => Carbon::now()->format('Y-m-d'),
                'perihal' => $validated['perihal'],
                'jenis_surat' => $validated['jenisSurat'],
                'klasifikasi' => $klasifikasi,
                'edoc_kartu_tanda_penduduk' => $filePaths['ktp'],
                'edoc_kartu_tanda_anggota' => $filePaths['kta'] ?? null, // Advokat
                'edoc_kartu_tanda_pegawai' => $filePaths['ktpp'] ?? null, // Non-Advokat
                'edoc_berita_acara_sumpah' => $filePaths['bas'] ?? null, // Advokat
                'edoc_surat_tugas' => $filePaths['suratTugas'] ?? null, // Non-Advokat
                'edoc_surat_kuasa' => $filePaths['suratKuasa'],
                'tahapan' => TahapanSuratKuasaEnum::Pendaftaran->value,
                'user_id' => Auth::user()->id,
                'pemohon' => Auth::user()->name
            ]);

            // 3. Decode and Save Parties
            $pemberiKuasa = json_decode($pemberiKuasaJson, true);
            $penerimaKuasa = json_decode($penerimaKuasaJson, true);

            // Save Pemberi Kuasa
            foreach ($pemberiKuasa as $pihakPemberi) {
                PihakSuratKuasaModel::create([
                    'surat_kuasa_id' => $pendaftaran->id,
                    'nik' => $pihakPemberi['nik'],
                    'nama' => $pihakPemberi['nama'],
                    'pekerjaan' => $pihakPemberi['pekerjaan'],
                    'alamat' => $pihakPemberi['alamat'],
                    'jenis' => PihakSuratKuasaEnum::Pemberi->value,
                ]);
            }

            // Save Penerima Kuasa
            foreach ($penerimaKuasa as $pihakPenerima) {
                PihakSuratKuasaModel::create([
                    'surat_kuasa_id' => $pendaftaran->id,
                    'nik' => $pihakPenerima['nik'],
                    'nama' => $pihakPenerima['nama'],
                    'pekerjaan' => $pihakPenerima['pekerjaan'],
                    'alamat' => $pihakPenerima['alamat'],
                    'jenis' => PihakSuratKuasaEnum::Penerima->value
                ]);
            }

            DB::commit();

            Log::info('Registration of power of attorney was successful', $validated);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran surat kuasa berhasil diajukan.',
                'id' => Crypt::encrypt($pendaftaran->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('local')->deleteDirectory($uploadPath);
            Log::error('Failed to save registration of power of attorney: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server saat menyimpan data.'], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $id = Crypt::decrypt($id);
        $pendaftaran = PendaftaranSuratKuasaModel::findOrFail($id);
        $klasifikasi = $pendaftaran->klasifikasi;

        // Get data pihak from (JSON string) request
        $pemberiKuasaJson = $request->input('pemberi_kuasa');
        $penerimaKuasaJson = $request->input('penerima_kuasa');

        // Determine which request rules to use based on 'jenis'
        $formRequest = null;
        if ($klasifikasi == SuratKuasaEnum::Advokat->value) {
            $formRequest = new SuratKuasaAdvokatRequest();
        } elseif ($klasifikasi == SuratKuasaEnum::NonAdvokat->value) {
            $formRequest = new SuratKuasaNonAdvokatRequest();
        } else {
            return response()->json(['message' => 'Jenis surat kuasa tidak valid.'], 400);
        }

        // Validate for update, making file fields optional
        $validated = $request->validate($formRequest->rules(true), $formRequest->messages());

        DB::beginTransaction();
        try {
            // 1. Handle File Uploads
            $filePaths = [];
            $uploadPath = 'surat-kuasa/' . date('m') . '/' . date('Y') . '/' . $pendaftaran->id_daftar;

            $fileFields = ($klasifikasi === SuratKuasaEnum::Advokat->value)
                ? ['ktp' => 'edoc_kartu_tanda_penduduk', 'kta' => 'edoc_kartu_tanda_anggota', 'bas' => 'edoc_berita_acara_sumpah', 'suratKuasa' => 'edoc_surat_kuasa']
                : ['ktp' => 'edoc_kartu_tanda_penduduk', 'ktpp' => 'edoc_kartu_tanda_pegawai', 'suratTugas' => 'edoc_surat_tugas', 'suratKuasa' => 'edoc_surat_kuasa'];

            foreach ($fileFields as $field => $dbColumn) {
                if ($request->hasFile($field)) {
                    // Delete old file
                    if ($pendaftaran->$dbColumn && Storage::disk('local')->exists($pendaftaran->$dbColumn)) {
                        Storage::disk('local')->delete($pendaftaran->$dbColumn);
                    }
                    // Store new file
                    $filePaths[$dbColumn] = $request->file($field)->store($uploadPath, 'local');
                }
            }

            // Update name of user pemohon
            $user = User::find($pendaftaran->user_id);

            // 2. Update Main Registration Record
            $updateData = [
                'perihal' => $validated['perihal'],
                'jenis_surat' => $validated['jenisSurat'],
                'tahapan' => TahapanSuratKuasaEnum::PerbaikanData->value,
                'status' => null, // Reset status
                'pemohon' => $user->name
            ];

            // Merge file paths into update data
            $pendaftaran->update(array_merge($updateData, $filePaths));

            // 3. Decode and Sync Parties
            $pemberiKuasa = json_decode($pemberiKuasaJson, true);
            $penerimaKuasa = json_decode($penerimaKuasaJson, true);

            // Delete old parties
            $pendaftaran->pihak()->delete();

            // Save Pemberi Kuasa
            foreach ($pemberiKuasa as $pihakPemberi) {
                PihakSuratKuasaModel::create([
                    'surat_kuasa_id' => $pendaftaran->id,
                    'nik' => $pihakPemberi['nik'],
                    'nama' => $pihakPemberi['nama'],
                    'pekerjaan' => $pihakPemberi['pekerjaan'],
                    'alamat' => $pihakPemberi['alamat'],
                    'jenis' => PihakSuratKuasaEnum::Pemberi->value,
                ]);
            }

            // Save Penerima Kuasa
            foreach ($penerimaKuasa as $pihakPenerima) {
                PihakSuratKuasaModel::create([
                    'surat_kuasa_id' => $pendaftaran->id,
                    'nik' => $pihakPenerima['nik'],
                    'nama' => $pihakPenerima['nama'],
                    'pekerjaan' => $pihakPenerima['pekerjaan'],
                    'alamat' => $pihakPenerima['alamat'],
                    'jenis' => PihakSuratKuasaEnum::Penerima->value
                ]);
            }

            DB::commit();

            Log::info('Update of power of attorney was successful', $validated);

            return response()->json(['success' => true, 'message' => 'Data pendaftaran surat kuasa berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update registration of power of attorney: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server saat memperbarui data.'], 500);
        }
    }

    public function destroy() {}
}
