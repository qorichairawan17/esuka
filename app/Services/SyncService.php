<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Support\Str;
use App\Enum\SuratKuasaEnum;
use App\Enum\PihakSuratKuasaEnum;
use App\Enum\StatusSuratKuasaEnum;
use Illuminate\Support\Facades\DB;
use App\Enum\TahapanSuratKuasaEnum;
use Illuminate\Support\Facades\Log;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pengguna\PaniteraModel;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Jobs\GenerateBarcodeSuratKuasaPDF;
use App\Models\Suratkuasa\PihakSuratKuasaModel;
use App\Models\Sync\StagingSyncSuratKuasaModel;
use App\Models\Suratkuasa\RegisterSuratKuasaModel;
use App\Models\Suratkuasa\PembayaranSuratKuasaModel;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Support\Facades\Hash;

class SyncService
{
    private $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    public function fetchData($klasifikasi)
    {
        $query = $this->buildQuery($klasifikasi);
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid klasifikasi parameter'
            ], 400);
        }

        Log::info('Starting fetch data surat kuasa old on Database');
        $data = DB::connection('sync_staging')->select($query);

        $syncedCount = 0;
        DB::beginTransaction();
        try {
            foreach ($data as $item) {
                StagingSyncSuratKuasaModel::updateOrCreate(
                    ['source_id' => $item->surat_kuasa_id],
                    [
                        'user_id' => $item->user_id,
                        'tanggal_daftar' => $item->tanggal_daftar,
                        'email' => $item->email,
                        'nama_lengkap' => $item->nama_lengkap,
                        'perihal' => $item->perihal,
                        'jenis_surat' => $item->jenis_surat,
                        'edoc_kartu_tanda_penduduk' => $item->edoc_kartu_tanda_penduduk,
                        'edoc_kartu_tanda_anggota' => $item->edoc_kartu_tanda_anggota,
                        'edoc_kartu_tanda_pegawai' => $item->edoc_kartu_tanda_pegawai,
                        'edoc_berita_acara_sumpah' => $item->edoc_berita_acara_sumpah,
                        'edoc_surat_tugas' => $item->edoc_surat_tugas,
                        'edoc_surat_kuasa' => $item->edoc_surat_kuasa,
                        'id_pemberi' => $item->id_pemberi,
                        'nik_pemberi' => $item->nik_pemberi,
                        'nama_pemberi' => $item->nama_pemberi,
                        'pekerjaan_pemberi' => $item->pekerjaan_pemberi,
                        'alamat_pemberi' => $item->alamat_pemberi,
                        'id_penerima' => $item->id_penerima,
                        'nik_penerima' => $item->nik_penerima,
                        'nama_penerima' => $item->nama_penerima,
                        'pekerjaan_penerima' => $item->pekerjaan_penerima,
                        'alamat_penerima' => $item->alamat_penerima,
                        'bukti_pembayaran' => $item->bukti_pembayaran,
                        'status' => $item->status,
                        'tanggal_bayar' => $item->tanggal_bayar,
                        'keterangan' => $item->keterangan,
                        'panitera' => $item->panitera,
                        'nomor_surat_kuasa' => $item->nomor_surat_kuasa,
                        'tanggal_disetujui' => $item->tanggal_disetujui,
                        'klasifikasi' => $klasifikasi,
                    ]
                );
                $syncedCount++;
            }

            DB::commit();
            Log::info('Successfully synchronized Surat Kuasa data.', ['inserted_count' => $syncedCount]);

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi data " . $syncedCount . " Surat Kuasa berhasil",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during Surat Kuasa data synchronization.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during Surat Kuasa data synchronization.',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function buildQuery(string $klasifikasi): ?string
    {
        $baseQuery = "
            SELECT
                sk.id AS surat_kuasa_id,
                sk.id_pengguna AS user_id,
                sk.created_at AS tanggal_daftar,
                u.email,
                p.nama_lengkap,
                sk.perihal,
                sk.jenis AS jenis_surat,
                sk.ktp AS edoc_kartu_tanda_penduduk,
                sk.kartu_advokat AS edoc_kartu_tanda_anggota,
                sk.kartu_tanda_pegawai AS edoc_kartu_tanda_pegawai,
                sk.ba_sumpah AS edoc_berita_acara_sumpah,
                sk.surat_tugas AS edoc_surat_tugas,
                sk.surat_kuasa AS edoc_surat_kuasa,
                CONCAT('[', GROUP_CONCAT(DISTINCT phkpemberi.id), ']') AS id_pemberi,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpemberi.nik_pemberi, '\"'), ']') AS nik_pemberi,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpemberi.nama_pemberi, '\"'), ']') AS nama_pemberi,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpemberi.pekerjaan_pemberi, '\"'), ']') AS pekerjaan_pemberi,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpemberi.alamat_pemberi, '\"'), ']') AS alamat_pemberi,
                CONCAT('[', GROUP_CONCAT(DISTINCT phkpenerima.id), ']') AS id_penerima,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpenerima.nik_penerima, '\"'), ']') AS nik_penerima,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpenerima.nama_penerima, '\"'), ']') AS nama_penerima,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpenerima.pekerjaan_penerima, '\"'), ']') AS pekerjaan_penerima,
                CONCAT('[', GROUP_CONCAT(DISTINCT '\"', phkpenerima.alamat_penerima, '\"'), ']') AS alamat_penerima,
                byr.photo_bukti as bukti_pembayaran,
                byr.pesan AS keterangan,
                byr.created_at AS tanggal_bayar,
                statusbyr.keterangan AS status,
                register.panitera,
                register.no_pendaftaran AS nomor_surat_kuasa,
                register.created_at AS tanggal_disetujui
            FROM
                data_surat_kuasa sk
            JOIN auth_pengguna u ON sk.id_pengguna = u.id
            LEFT JOIN profil_pengguna p ON u.id = p.id_pengguna
            LEFT JOIN data_pemberi_kuasa phkpemberi ON sk.id = phkpemberi.id_surat_kuasa
            LEFT JOIN data_penerima_kuasa phkpenerima ON sk.id = phkpenerima.id_surat_kuasa
            LEFT JOIN data_bukti_pembayaran byr ON sk.id = byr.id_surat_kuasa
            LEFT JOIN status_pembayaran statusbyr ON byr.id_status_pembayaran = statusbyr.status
            LEFT JOIN data_surat_pembayaran register ON sk.id = register.id_surat_kuasa
            WHERE u.id_role = '2'
        ";

        $whereClause = "";
        if ($klasifikasi == SuratKuasaEnum::Advokat->value) {
            $whereClause = " AND sk.kartu_advokat IS NOT NULL";
        } elseif ($klasifikasi == SuratKuasaEnum::NonAdvokat->value) {
            $whereClause = " AND sk.kartu_tanda_pegawai IS NOT NULL";
        } else {
            return null;
        }

        $groupByClause = " GROUP BY sk.id, sk.created_at, u.id, u.email, p.nama_lengkap, sk.perihal, sk.jenis, sk.ktp, sk.kartu_advokat, sk.kartu_tanda_pegawai, sk.ba_sumpah, sk.surat_tugas, sk.surat_kuasa, byr.photo_bukti, byr.created_at, byr.pesan, statusbyr.keterangan, register.panitera, register.no_pendaftaran, register.created_at";

        return $baseQuery . $whereClause . $groupByClause;
    }

    public function fetchShow($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = StagingSyncSuratKuasaModel::findOrFail($decryptedId);

            return response()->json($data);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Failed to decrypt ID for sync show detail: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'ID tidak valid.'], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function fetchDelete()
    {
        try {
            StagingSyncSuratKuasaModel::truncate();
            return response()->json([
                'success' => true,
                'message' => 'Semua data staging sinkronisasi berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error truncating staging_sync_surat_kuasa table.', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data staging.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reads a file from a local path, encrypts its content, and stores it in a new location.
     *
     * @param string|null $sourcePath The relative path within the storage/app directory (e.g., '/staging/pdf/file.pdf').
     * @param string $destinationPath The desired local path for the stored file (e.g., 'migrated_edocs/2023/10/file.pdf').
     * @return string|null The full local path to the stored file, or null if the source file doesn't exist or storage failed.
     */
    private function moveAndEncryptFile(?string $sourcePath, string $destinationPath): ?string
    {
        if (empty($sourcePath) || !Storage::disk('local')->exists($sourcePath)) {
            Log::warning("Source file not found for migration: " . $sourcePath);
            return $sourcePath;
        }

        try {
            $fileContent = Storage::disk('local')->get($sourcePath);
            $encryptedContent = Crypt::encryptString($fileContent);
            Storage::disk('local')->put($destinationPath, $encryptedContent);

            return $destinationPath;
        } catch (\Exception $e) {
            Log::error("Failed to move and encrypt file from {$sourcePath} to {$destinationPath}. Error: " . $e->getMessage());
            return $sourcePath;
        }
    }

    /**
     * Parses a JSON string that might contain an array of values, handling nulls and malformed data.
     *
     * @param string|null $jsonString The JSON string to parse.
     * @return array The parsed array, or an empty array if parsing fails or input is invalid.
     */
    private function parseJsonArray(?string $jsonString): array
    {
        if (empty($jsonString)) {
            return [];
        }
        $decoded = json_decode($jsonString, true);
        // Handle cases where GROUP_CONCAT might return JSON like `["null"]` or `[null]`
        if (is_array($decoded) && count($decoded) === 1 && ($decoded[0] === null || $decoded[0] === 'null')) {
            return [];
        }
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Migrates data from StagingSyncSuratKuasaModel to the main application models.
     *
     * @return array A summary of the migration results.
     */
    public function migrateStagingData(): array
    {
        $stagingRecords = StagingSyncSuratKuasaModel::all();
        $migratedCount = 0;
        $failedCount = 0;
        $errors = [];

        Log::info('Starting migration of staging data to main application models.');

        foreach ($stagingRecords as $item) {
            DB::beginTransaction();
            try {
                $currentYear = Carbon::now()->year;
                $currentMonth = Carbon::now()->month;
                $uploadBaseDir = "migrated_edocs/{$currentYear}/{$currentMonth}/{$item->source_id}";

                // 1. Find or Create User
                $user = User::where('email', $item->email)->first();
                if (!$user) {
                    // Parse full name into first and last name
                    $nameParts = explode(' ', trim($item->nama_lengkap ?? $item->email), 2);
                    $namaDepan = $nameParts[0] ?: '-'; // Use '-' if the first name is empty
                    $namaBelakang = $nameParts[1] ?? '-'; // Use '-' if the last name is not present

                    // Create a new profile for the user
                    $profile = ProfileModel::create([
                        'nama_depan' => $namaDepan,
                        'nama_belakang' => $namaBelakang,
                    ]);

                    // Create the new user and link it to the profile
                    $user = User::create([
                        'name' => $item->nama_lengkap ?? $item->email,
                        'email' => Str::lower($item->email),
                        'password' => Hash::make(Str::random(8)), // Generate a random password, user can reset later
                        'email_verified_at' => Carbon::now(),
                        'reactivation' => '1',
                        'role' => RoleEnum::User->value, // Set default role
                        'profile_id' => $profile->id, // Link to the created profile
                        'profile_status' => '0', // Set default profile status as incomplete
                    ]);
                    Log::info("Created new user with profile during migration: {$user->email}");
                }

                // 2. Move and encrypt file from local storage
                $filePaths = [];
                $docMap = [
                    'edoc_kartu_tanda_penduduk' => ['source_dir' => 'staging/pdf', 'prefix' => 'ktp'],
                    'edoc_kartu_tanda_anggota' => ['source_dir' => 'staging/pdf', 'prefix' => 'kta'],
                    'edoc_kartu_tanda_pegawai' => ['source_dir' => 'staging/pdf', 'prefix' => 'ktpp'],
                    'edoc_berita_acara_sumpah' => ['source_dir' => 'staging/pdf', 'prefix' => 'bas'],
                    'edoc_surat_tugas' => ['source_dir' => 'staging/pdf', 'prefix' => 'surat_tugas'],
                    'edoc_surat_kuasa' => ['source_dir' => 'staging/pdf', 'prefix' => 'surat_kuasa'],
                    'bukti_pembayaran' => ['source_dir' => 'staging/pembayaran', 'prefix' => 'bukti_pembayaran'],
                ];

                foreach ($docMap as $field => $config) {
                    $sourceFileName = $item->$field;
                    if (!empty($sourceFileName)) {
                        $sourcePath = "{$config['source_dir']}/{$sourceFileName}";
                        $extension = pathinfo($sourceFileName, PATHINFO_EXTENSION) ?: 'pdf';
                        $destinationPath = "{$uploadBaseDir}/{$config['prefix']}_" . Str::random(10) . ".{$extension}";

                        // Pindahkan dan enkripsi file. Fungsi ini akan mengembalikan path sumber jika gagal.
                        $filePaths[$field] = $this->moveAndEncryptFile($sourcePath, $destinationPath);
                    } else {
                        // Jika nama file di staging DB kosong, set path ke null.
                        $filePaths[$field] = null;
                    }
                }

                // 3. Determine the new status based on the old status value
                $newStatus = null;
                if ($item->status === StatusSuratKuasaEnum::Disetujui->value) {
                    $newStatus = StatusSuratKuasaEnum::Disetujui->value;
                } elseif ($item->status === StatusSuratKuasaEnum::Ditolak->value) {
                    $newStatus = StatusSuratKuasaEnum::Ditolak->value;
                }

                // 3. Create or Update PendaftaranSuratKuasaModel
                // We use a combination of user_id and perihal to find existing records.
                // A dedicated `source_id` column would be more reliable.
                $pendaftaran = PendaftaranSuratKuasaModel::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'perihal' => $item->perihal,
                    ],
                    [
                        'id_daftar' => '#' . $this->infoApp->kode_dipa . '-' . Str::upper(Str::random(3)) . Str::numbers(3),
                        'tanggal_daftar' => Carbon::parse($item->tanggal_daftar)->format('Y-m-d') ?? Carbon::now(),
                        'jenis_surat' => $item->jenis_surat,
                        'klasifikasi' => $item->klasifikasi,
                        'edoc_kartu_tanda_penduduk' => $filePaths['edoc_kartu_tanda_penduduk'],
                        'edoc_kartu_tanda_anggota' => $filePaths['edoc_kartu_tanda_anggota'],
                        'edoc_kartu_tanda_pegawai' => $filePaths['edoc_kartu_tanda_pegawai'],
                        'edoc_berita_acara_sumpah' => $filePaths['edoc_berita_acara_sumpah'],
                        'edoc_surat_tugas' => $filePaths['edoc_surat_tugas'],
                        'edoc_surat_kuasa' => $filePaths['edoc_surat_kuasa'],
                        'tahapan' => $newStatus === StatusSuratKuasaEnum::Disetujui->value ? TahapanSuratKuasaEnum::Verifikasi->value : TahapanSuratKuasaEnum::Pendaftaran->value,
                        'status' => $newStatus,
                        'keterangan' => $item->keterangan,
                        'pemohon' => $item->nama_lengkap ?? $item->email,
                        'created_at' => $item->tanggal_daftar ?? Carbon::now(), // Preserve original creation date
                        'updated_at' => $item->tanggal_daftar ?? Carbon::now(), // Preserve original update date
                    ]
                );

                $idDaftar = $pendaftaran->id_daftar;
                $wasCreated = $pendaftaran->wasRecentlyCreated;

                // If the record was updated, clear existing related data before adding new ones.
                if (!$wasCreated) {
                    Log::info("Updating existing registration during migration: {$idDaftar}");
                    $pendaftaran->pihak()->delete();
                    $pendaftaran->pembayaran()->delete();
                    // We keep the register record to avoid re-generating barcode, but you might want to update it.
                }

                // 4. Create PihakSuratKuasaModel (Pemberi)
                $namaPemberi = $this->parseJsonArray($item->nama_pemberi);
                $nikPemberi = $this->parseJsonArray($item->nik_pemberi);
                $pekerjaanPemberi = $this->parseJsonArray($item->pekerjaan_pemberi);
                $alamatPemberi = $this->parseJsonArray($item->alamat_pemberi);

                foreach ($namaPemberi as $idx => $nama) {
                    PihakSuratKuasaModel::create([
                        'surat_kuasa_id' => $pendaftaran->id,
                        'nik' => $nikPemberi[$idx] ?? '-',
                        'nama' => $nama,
                        'pekerjaan' => $pekerjaanPemberi[$idx] ?? '-',
                        'alamat' => $alamatPemberi[$idx] ?? '-',
                        'jenis' => PihakSuratKuasaEnum::Pemberi->value,
                    ]);
                }

                // 5. Create PihakSuratKuasaModel (Penerima)
                $namaPenerima = $this->parseJsonArray($item->nama_penerima);
                $nikPenerima = $this->parseJsonArray($item->nik_penerima);
                $pekerjaanPenerima = $this->parseJsonArray($item->pekerjaan_penerima);
                $alamatPenerima = $this->parseJsonArray($item->alamat_penerima);

                foreach ($namaPenerima as $idx => $nama) {
                    PihakSuratKuasaModel::create([
                        'surat_kuasa_id' => $pendaftaran->id,
                        'nik' => $nikPenerima[$idx] ?? '-',
                        'nama' => $nama,
                        'pekerjaan' => $pekerjaanPenerima[$idx] ?? '-',
                        'alamat' => $alamatPenerima[$idx] ?? '-',
                        'jenis' => PihakSuratKuasaEnum::Penerima->value,
                    ]);
                }

                // 6. Create or Update PembayaranSuratKuasaModel (if applicable)
                if ($item->tanggal_bayar && ($filePaths['bukti_pembayaran'] ?? null)) {
                    PembayaranSuratKuasaModel::updateOrCreate(
                        ['surat_kuasa_id' => $pendaftaran->id],
                        [
                            'tanggal_pembayaran' => Carbon::parse($item->tanggal_bayar)->format('Y-m-d'),
                            'jenis_pembayaran' => 'Migrasi Data', // Default type for migrated payments
                            'bukti_pembayaran' => $filePaths['bukti_pembayaran'],
                            'user_payment_id' => $user->id, // User who made the payment in the old system
                            'created_at' => $item->tanggal_bayar,
                            'updated_at' => $item->tanggal_bayar,
                        ]
                    );
                }

                // 7. Create or Update RegisterSuratKuasaModel (if approved)
                if ($item->status === StatusSuratKuasaEnum::Disetujui->value && $item->nomor_surat_kuasa) {
                    $panitera = PaniteraModel::where('nama', $item->panitera)->first();
                    $paniteraId = $panitera ? $panitera->id : null; // Assign to null if panitera not found

                    $register = RegisterSuratKuasaModel::updateOrCreate(
                        ['surat_kuasa_id' => $pendaftaran->id],
                        [
                            'uuid' => Str::uuid(),
                            'tanggal_register' => Carbon::parse($item->tanggal_disetujui)->format('Y-m-d'),
                            'nomor_surat_kuasa' => $item->nomor_surat_kuasa,
                            'approval_id' => Auth::id(), // Admin performing the sync
                            'panitera_id' => $paniteraId,
                            'path_file' => '', // Will be updated by job
                            'created_at' => $item->tanggal_disetujui,
                            'updated_at' => $item->tanggal_disetujui,
                        ]
                    );

                    // Dispatch job to generate barcode PDF if it's a new registration or path is missing
                    if ($register->wasRecentlyCreated || empty($register->path_file)) {
                        GenerateBarcodeSuratKuasaPDF::dispatchSync($register);
                    }
                }

                DB::commit();
                $migratedCount++;
                $actionVerb = $wasCreated ? 'migrated' : 'updated';
                Log::info("Successfully {$actionVerb} staging record with source_id: {$item->source_id} to id_daftar: {$idDaftar}");

                // Record audit trail for the migration of this record
                AuditTrailService::record(
                    "telah {$actionVerb} data surat kuasa lama dengan ID sumber " . $item->source_id . ' ke pendaftaran dengan nomor ' . $idDaftar,
                    ['old_staging_data' => $item->toArray(), 'new_pendaftaran_id' => $pendaftaran->id]
                );
            } catch (\Exception $e) {
                DB::rollBack();
                $failedCount++;
                $errors[] = "Failed to migrate source_id {$item->source_id}: " . $e->getMessage();
                Log::error("Failed to migrate staging record with source_id: {$item->source_id}. Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                if (isset($uploadBaseDir) && Storage::disk('local')->exists($uploadBaseDir)) {
                    Storage::disk('local')->deleteDirectory($uploadBaseDir);
                }
            }
        }

        Log::info('Migration process completed.', ['migrated_count' => $migratedCount, 'failed_count' => $failedCount, 'errors' => $errors]);

        return [
            'total_records' => $stagingRecords->count(),
            'migrated_count' => $migratedCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
    }
}
