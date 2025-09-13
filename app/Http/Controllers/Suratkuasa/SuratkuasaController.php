<?php

namespace App\Http\Controllers\Suratkuasa;

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
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
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
            if ($suratKuasa->tahapan !== TahapanSuratKuasaEnum::PerbaikanData->value) {
                return redirect()->route('surat-kuasa.detail', ['id' => $request->id])->with('error', 'Surat kuasa ini tidak dalam tahap perbaikan data.');
            }

            $pageTitle = 'Edit Surat Kuasa';
            $idDaftar = $suratKuasa->id_daftar;
        }

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

    /**
     * Generates a unique nomor surat kuasa in the format specified in the application settings.
     *
     * @return string The generated nomor surat kuasa.
     */
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
            'title' => 'Surat Kuasa ' . $suratKuasa->pemohon . ' - ' . config('app.name'),
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
            // Decrypt the file path from the request
            $filePath = Crypt::decrypt($request->path);

            // Check if the file exists in storage
            if (Storage::disk('local')->exists($filePath)) {
                // Get encrypted content, decrypt it, and then create a response
                $encryptedContent = Storage::disk('local')->get($filePath);
                $decryptedContent = Crypt::decryptString($encryptedContent);

                // Create a response with the decrypted content
                return response($decryptedContent)->header('Content-Type', Storage::mimeType($filePath));
            }

            // Log an error if the file is not found
            Log::error('File not found: ' . $filePath);
            // Return a 404 error if the file is not found
            return abort(404, 'File tidak ditemukan.');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Log an error if there is an error decrypting the file path
            Log::error('Error decrypting file path: ' . $e->getMessage());
            // Return a 404 error if there is an error decrypting the file path
            return abort(404, 'Path file tidak valid.');
        }
    }

    public function previewFile(Request $request)
    {
        try {
            // Decrypt id and jenis dokumen from request
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
                'Content-Disposition' => 'inline; filename="' . $newFileName . '"',
                'Content-Type' => Storage::mimeType($filePath)
            ];

            // Get encrypted content, decrypt it, and then create a response
            $encryptedContent = Storage::disk('local')->get($filePath);
            $decryptedContent = Crypt::decryptString($encryptedContent);

            return response($decryptedContent, 200, $headers);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption error
            Log::error('Error decrypting preview file path: ' . $e->getMessage());
            return abort(404, 'ID pendaftaran tidak valid.');
        }
    }

    public function store(Request $request): JsonResponse
    {
        $uploadPath = null; // Initialize to null
        DB::beginTransaction();
        try {
            $klasifikasi = $request->input('klasifikasi');
            $idDaftar = $request->input('idDaftar');

            // 1. Validate request based on classification
            $formRequest = $this->getFormRequestForKlasifikasi($klasifikasi);
            $validated = $request->validate($formRequest->rules(false), $formRequest->messages());

            // 2. Handle File Uploads
            $uploadPath = 'surat-kuasa/' . date('m') . '/' . date('Y') . '/' . $idDaftar;
            $filePaths = $this->storePendaftaranFiles($request, $klasifikasi, $uploadPath);

            // 3. Create Main Registration Record
            $pendaftaran = $this->createPendaftaranRecord($validated, $filePaths, $klasifikasi, $idDaftar);

            // 4. Decode and Save Parties
            $this->createPihak($pendaftaran, $request->input('pemberi_kuasa'), PihakSuratKuasaEnum::Pemberi);
            $this->createPihak($pendaftaran, $request->input('penerima_kuasa'), PihakSuratKuasaEnum::Penerima);

            DB::commit();

            Log::info('Power Attorney Registration submitted successfully', ['id_daftar' => $idDaftar]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran surat kuasa berhasil diajukan.',
                'id' => Crypt::encrypt($pendaftaran->id)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // The validation exception is already handled by Laravel, but we catch it to prevent it from being caught by the generic Exception handler.
            // It will automatically return a 422 response.
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($uploadPath) {
                Storage::disk('local')->deleteDirectory($uploadPath);
            }
            Log::error('Error saving power attorney registration: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server saat menyimpan data.'], 500);
        }
    }

    /**
     * Get the appropriate form request class based on the classification.
     *
     * @param string $klasifikasi
     * @return SuratKuasaAdvokatRequest|SuratKuasaNonAdvokatRequest
     * @throws \Exception
     */
    private function getFormRequestForKlasifikasi(string $klasifikasi)
    {
        if ($klasifikasi === SuratKuasaEnum::Advokat->value) {
            return new SuratKuasaAdvokatRequest();
        }

        if ($klasifikasi === SuratKuasaEnum::NonAdvokat->value) {
            return new SuratKuasaNonAdvokatRequest();
        }

        throw new \Exception('Jenis surat kuasa tidak valid.');
    }

    /**
     * Store uploaded files for a new registration.
     *
     * @param Request $request
     * @param string $klasifikasi
     * @param string $uploadPath
     * @return array
     */
    private function storePendaftaranFiles(Request $request, string $klasifikasi, string $uploadPath): array
    {
        $filePaths = [];
        $fileFields = ($klasifikasi === SuratKuasaEnum::Advokat->value)
            ? ['ktp', 'kta', 'bas', 'suratKuasa']
            : ['ktp', 'ktpp', 'suratTugas', 'suratKuasa'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                // Generate a unique name but keep the extension for mime-type detection later
                $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $fullPath = "{$uploadPath}/{$fileName}";

                // Encrypt content and store
                Storage::disk('local')->put($fullPath, Crypt::encryptString($file->get()));
                $filePaths[$field] = $fullPath;
            }
        }
        return $filePaths;
    }

    /**
     * Create the main registration record.
     *
     * @param array $validated
     * @param array $filePaths
     * @param string $klasifikasi
     * @param string $idDaftar
     * @return PendaftaranSuratKuasaModel
     */
    private function createPendaftaranRecord(array $validated, array $filePaths, string $klasifikasi, string $idDaftar): PendaftaranSuratKuasaModel
    {
        return PendaftaranSuratKuasaModel::create([
            'id_daftar' => $idDaftar,
            'tanggal_daftar' => Carbon::now()->format('Y-m-d'),
            'perihal' => $validated['perihal'],
            'jenis_surat' => $validated['jenisSurat'],
            'klasifikasi' => $klasifikasi,
            'edoc_kartu_tanda_penduduk' => $filePaths['ktp'] ?? null,
            'edoc_kartu_tanda_anggota' => $filePaths['kta'] ?? null,
            'edoc_kartu_tanda_pegawai' => $filePaths['ktpp'] ?? null,
            'edoc_berita_acara_sumpah' => $filePaths['bas'] ?? null,
            'edoc_surat_tugas' => $filePaths['suratTugas'] ?? null,
            'edoc_surat_kuasa' => $filePaths['suratKuasa'] ?? null,
            'tahapan' => TahapanSuratKuasaEnum::Pendaftaran->value,
            'user_id' => Auth::id(),
            'pemohon' => Auth::user()->name
        ]);
    }

    /**
     * Create party records (pemberi/penerima kuasa).
     *
     * @param PendaftaranSuratKuasaModel $pendaftaran
     * @param string|null $pihakJson
     * @param PihakSuratKuasaEnum $jenis
     */
    private function createPihak(PendaftaranSuratKuasaModel $pendaftaran, ?string $pihakJson, PihakSuratKuasaEnum $jenis): void
    {
        if (is_null($pihakJson)) return;

        $pihakArray = json_decode($pihakJson, true);

        if (is_array($pihakArray)) {
            foreach ($pihakArray as $pihak) {
                PihakSuratKuasaModel::create([
                    'surat_kuasa_id' => $pendaftaran->id,
                    'nik' => $pihak['nik'],
                    'nama' => $pihak['nama'],
                    'pekerjaan' => $pihak['pekerjaan'],
                    'alamat' => $pihak['alamat'],
                    'jenis' => $jenis->value,
                ]);
            }
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $decryptedId = Crypt::decrypt($id);
            $pendaftaran = PendaftaranSuratKuasaModel::findOrFail($decryptedId);
            $klasifikasi = $pendaftaran->klasifikasi;

            // 1. Validate request
            $formRequest = $this->getFormRequestForKlasifikasi($klasifikasi);
            $validated = $request->validate($formRequest->rules(true), $formRequest->messages());

            // 2. Handle file updates
            $filePaths = $this->updatePendaftaranFiles($request, $pendaftaran, $klasifikasi);

            // 3. Update main registration record
            $this->updatePendaftaranRecord($pendaftaran, $validated, $filePaths);

            // 4. Sync parties (pemberi and penerima kuasa)
            $this->syncAllPihak(
                $pendaftaran,
                $request->input('pemberi_kuasa'),
                $request->input('penerima_kuasa')
            );

            DB::commit();

            Log::info('Update pendaftaran surat kuasa berhasil', ['id' => $pendaftaran->id]);

            return response()->json(['success' => true, 'message' => 'Data pendaftaran surat kuasa berhasil diperbarui.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation exceptions are re-thrown to be handled by Laravel's default handler (422 response).
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui pendaftaran surat kuasa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server saat memperbarui data.'], 500);
        }
    }

    /**
     * Update the main registration record.
     *
     * @param PendaftaranSuratKuasaModel $pendaftaran
     * @param array $validated
     * @param array $filePaths
     */
    private function updatePendaftaranRecord(PendaftaranSuratKuasaModel $pendaftaran, array $validated, array $filePaths): void
    {
        $user = User::find($pendaftaran->user_id);

        $updateData = [
            'perihal' => $validated['perihal'],
            'jenis_surat' => $validated['jenisSurat'],
            'tahapan' => TahapanSuratKuasaEnum::PengajuanPerbaikanData->value,
            'status' => null, // Reset status for re-verification
            'pemohon' => $user->name,
        ];

        $pendaftaran->update(array_merge($updateData, $filePaths));
    }

    /**
     * Handle file updates for an existing registration.
     *
     * @param Request $request
     * @param PendaftaranSuratKuasaModel $pendaftaran
     * @param string $klasifikasi
     * @return array
     */
    private function updatePendaftaranFiles(Request $request, PendaftaranSuratKuasaModel $pendaftaran, string $klasifikasi): array
    {
        $filePaths = [];
        $uploadPath = 'surat-kuasa/' . date('m') . '/' . date('Y') . '/' . $pendaftaran->id_daftar;

        $fileFields = ($klasifikasi === SuratKuasaEnum::Advokat->value)
            ? ['ktp' => 'edoc_kartu_tanda_penduduk', 'kta' => 'edoc_kartu_tanda_anggota', 'bas' => 'edoc_berita_acara_sumpah', 'suratKuasa' => 'edoc_surat_kuasa']
            : ['ktp' => 'edoc_kartu_tanda_penduduk', 'ktpp' => 'edoc_kartu_tanda_pegawai', 'suratTugas' => 'edoc_surat_tugas', 'suratKuasa' => 'edoc_surat_kuasa'];

        foreach ($fileFields as $field => $dbColumn) {
            if ($request->hasFile($field)) {
                // Delete old file if it exists
                if ($pendaftaran->$dbColumn && Storage::disk('local')->exists($pendaftaran->$dbColumn)) {
                    Storage::disk('local')->delete($pendaftaran->$dbColumn);
                }
                // Store new file
                $file = $request->file($field);
                $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $fullPath = "{$uploadPath}/{$fileName}";
                Storage::disk('local')->put($fullPath, Crypt::encryptString($file->get()));
                $filePaths[$dbColumn] = $fullPath;
            }
        }
        return $filePaths;
    }

    /**
     * Sync all parties by deleting old ones and creating new ones.
     *
     * @param PendaftaranSuratKuasaModel $pendaftaran
     * @param string|null $pemberiJson
     * @param string|null $penerimaJson
     */
    private function syncAllPihak(PendaftaranSuratKuasaModel $pendaftaran, ?string $pemberiJson, ?string $penerimaJson): void
    {
        // Delete all existing parties for this registration
        $pendaftaran->pihak()->delete();

        // Create new parties from the request
        $this->createPihak($pendaftaran, $pemberiJson, PihakSuratKuasaEnum::Pemberi);
        $this->createPihak($pendaftaran, $penerimaJson, PihakSuratKuasaEnum::Penerima);
    }

    public function destroy(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // 1. Decrypt ID and find the main registration data along with its relations
            $id = Crypt::decrypt($request->id);
            $pendaftaran = PendaftaranSuratKuasaModel::with(['register', 'pembayaran', 'pihak'])->findOrFail($id);

            // 2. Collect all file paths to be deleted
            $filesToDelete = [];

            // a. Documents from the registration (KTP, KTA, BAS, etc.)
            $docColumns = [
                'edoc_kartu_tanda_penduduk',
                'edoc_kartu_tanda_anggota',
                'edoc_kartu_tanda_pegawai',
                'edoc_berita_acara_sumpah',
                'edoc_surat_tugas',
                'edoc_surat_kuasa',
            ];
            foreach ($docColumns as $column) {
                if (!empty($pendaftaran->$column)) {
                    $filesToDelete[] = $pendaftaran->$column;
                }
            }

            // b. Payment proof
            if ($pendaftaran->pembayaran && !empty($pendaftaran->pembayaran->bukti_pembayaran)) {
                $filesToDelete[] = $pendaftaran->pembayaran->bukti_pembayaran;
            }

            // c. Barcode PDF from registration
            if ($pendaftaran->register && !empty($pendaftaran->register->path_file)) {
                $filesToDelete[] = $pendaftaran->register->path_file;
            }

            // 3. Delete all related directories from storage
            $directories = array_map('dirname', $filesToDelete);
            $uniqueDirectories = array_unique($directories);

            foreach ($uniqueDirectories as $directory) {
                // Ensure we don't try to delete the root directory (e.g., if a path was just 'file.txt')
                // and that the directory exists before attempting deletion.
                if ($directory !== '.' && Storage::disk('local')->exists($directory)) {
                    Storage::disk('local')->deleteDirectory($directory);
                }
            }

            // 4. Delete records from the database (relations would be deleted by cascade,
            //    but we delete them manually for safety)
            $pendaftaran->register()->delete();
            $pendaftaran->pembayaran()->delete();
            $pendaftaran->pihak()->delete();

            // 5. Permanently delete the main registration record
            $pendaftaran->forceDelete();

            DB::commit();

            Log::info('Power of attorney registration and all related data have been successfully deleted.', ['id' => $id]);
            return response()->json(['success' => true, 'message' => 'Pendaftaran surat kuasa berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete power of attorney registration: ' . $e->getMessage(), ['id_encrypted' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
