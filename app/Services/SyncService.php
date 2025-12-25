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
use App\Jobs\MigrateStagingRecordJob;

class SyncService
{
    private $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 3600, function () {
            return AplikasiModel::first();
        });
    }

    /**
     * Test connection to staging database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            // Test the connection by running a simple query
            $connection = DB::connection('sync_staging');
            $pdo = $connection->getPdo();

            // Get server info
            $serverVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $serverInfo = $pdo->getAttribute(\PDO::ATTR_SERVER_INFO) ?? 'N/A';

            // Get connection details (masked for security)
            $host = config('database.connections.sync_staging.host');
            $port = config('database.connections.sync_staging.port');
            $database = config('database.connections.sync_staging.database');

            // Run a test query to verify actual connectivity
            $testQuery = $connection->select('SELECT 1 as test');

            Log::info('Staging database connection test successful.', [
                'host' => $host,
                'database' => $database,
                'server_version' => $serverVersion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Koneksi ke database staging berhasil!',
                'data' => [
                    'host' => $host,
                    'port' => $port,
                    'database' => $database,
                    'server_version' => $serverVersion,
                    'connection_status' => 'Connected',
                    'tested_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\PDOException $e) {
            Log::error('Staging database connection test failed (PDO Error).', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke database staging.',
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Staging database connection test failed.', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengetes koneksi database staging.',
                'error' => $e->getMessage()
            ], 500);
        }
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
                        'tanggal_daftar' => $item->tanggal_disetujui,
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
                'message' => 'Gagal sinkronisasi data surat kuasa',
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

        $groupByClause = " GROUP BY sk.id, sk.id_pengguna, sk.created_at, u.id, u.email, p.nama_lengkap, sk.perihal, sk.jenis, sk.ktp, sk.kartu_advokat, sk.kartu_tanda_pegawai, sk.ba_sumpah, sk.surat_tugas, sk.surat_kuasa, byr.photo_bukti, byr.created_at, byr.pesan, statusbyr.keterangan, register.panitera, register.no_pendaftaran, register.created_at";

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
        $totalRecords = StagingSyncSuratKuasaModel::count();
        Log::info("Starting to dispatch migration jobs for {$totalRecords} staging records.");

        // Using chunkById to process data in batches for memory efficiency
        StagingSyncSuratKuasaModel::query()->chunkById(100, function ($stagingRecords) {
            foreach ($stagingRecords as $record) {
                // Move processing logic to the Job
                // Replace with the Job you'll create
                MigrateStagingRecordJob::dispatch($record, Auth::id());
            }
        });

        return [
            'success' => true,
            'message' => "Proses migrasi untuk {$totalRecords} data telah dimulai di background. Silakan monitor log untuk melihat progresnya.",
        ];
    }

    /**
     * This method contains the logic to process a single staging record.
     * It is intended to be moved into the `handle()` method of a new Laravel Job.
     *
     * @param StagingSyncSuratKuasaModel $item The staging record to migrate.
     * @param int|null $authId The ID of the user who initiated the migration.
     */
    public function processSingleMigration(StagingSyncSuratKuasaModel $item, ?int $authId)
    {
        // Check if this record has already been migrated
        $existingPendaftaran = PendaftaranSuratKuasaModel::where('migrated_from_id', $item->source_id)->first();
        if ($existingPendaftaran) {
            Log::info("Skipping migration for staging record with source_id: {$item->source_id} as it already exists with id_daftar: {$existingPendaftaran->id_daftar}");
            // Optionally, delete the staging record if you are sure it's fully migrated.
            $item->delete();
            return;
        }

        DB::beginTransaction();
        $uploadBaseDir = null;
        try {
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;
            $uploadBaseDir = "migrated_edocs/{$currentYear}/{$currentMonth}/{$item->source_id}";

            // 1. Find or Create User (with race condition handling)
            $user = $this->findOrCreateUser($item);

            // 2. Move and encrypt files
            $filePaths = $this->migrateFiles($item, $uploadBaseDir);

            // 3. Determine new status
            $newStatus = match ($item->status) {
                StatusSuratKuasaEnum::Disetujui->value => StatusSuratKuasaEnum::Disetujui->value,
                StatusSuratKuasaEnum::Ditolak->value  => StatusSuratKuasaEnum::Ditolak->value,
                default => null,
            };

            // 4. Create or Update Pendaftaran
            $pendaftaran = $this->createOrUpdatePendaftaran($item, $user, $filePaths, $newStatus);
            $idDaftar = $pendaftaran->id_daftar; // Keep for logging
            $wasCreated = $pendaftaran->wasRecentlyCreated;

            if (!$wasCreated) {
                Log::info("Updating existing registration during migration: {$idDaftar}");
                $pendaftaran->pihak()->delete();
                $pendaftaran->pembayaran()->delete();
            }

            // 5. Create Pihak
            $this->createPihak($pendaftaran->id, PihakSuratKuasaEnum::Pemberi->value, $item->nama_pemberi, $item->nik_pemberi, $item->pekerjaan_pemberi, $item->alamat_pemberi);
            $this->createPihak($pendaftaran->id, PihakSuratKuasaEnum::Penerima->value, $item->nama_penerima, $item->nik_penerima, $item->pekerjaan_penerima, $item->alamat_penerima);

            // 6. Create Pembayaran
            $this->createPembayaran($item, $pendaftaran->id, $user->id, $filePaths['bukti_pembayaran'] ?? null);

            // 7. Create Register & Dispatch Barcode Job
            if ($newStatus === StatusSuratKuasaEnum::Disetujui->value && $item->nomor_surat_kuasa) {
                $this->createRegisterAndDispatchBarcode($item, $pendaftaran->id, $authId);
            }

            DB::commit();

            $actionVerb = $wasCreated ? 'migrated' : 'updated';
            Log::info("Successfully {$actionVerb} staging record with source_id: {$item->source_id} to id_daftar: {$idDaftar}");

            // delete the record from staging table after successful migration
            $item->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to migrate staging record with source_id: {$item->source_id}. Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($uploadBaseDir && Storage::disk('local')->exists($uploadBaseDir)) {
                Storage::disk('local')->deleteDirectory($uploadBaseDir);
            }
        }
    }

    /**
     * Find existing user or create new user with race condition handling.
     * This method handles the case where multiple staging records have the same email
     * and are being processed concurrently.
     *
     * @param StagingSyncSuratKuasaModel $item The staging record containing user info.
     * @return User The found or created user.
     * @throws \Exception If user cannot be found or created after retries.
     */
    private function findOrCreateUser(StagingSyncSuratKuasaModel $item): User
    {
        $userEmail = Str::lower(trim($item->email));

        // First, try to find existing user
        $user = User::where('email', $userEmail)->first();

        if ($user) {
            Log::info("Found existing user during migration: {$user->email}");
            return $user;
        }

        // User doesn't exist, try to create with race condition handling
        try {
            // Use database lock to prevent race condition
            return DB::transaction(function () use ($userEmail, $item) {
                // Double-check inside transaction (in case another process created it)
                $user = User::where('email', $userEmail)->lockForUpdate()->first();

                if ($user) {
                    Log::info("Found existing user (after lock) during migration: {$user->email}");
                    return $user;
                }

                // Create Profile first
                $nameParts = explode(' ', trim($item->nama_lengkap ?? $item->email), 2);
                $profile = ProfileModel::create([
                    'nama_depan' => $nameParts[0] ?: '-',
                    'nama_belakang' => $nameParts[1] ?? '-',
                ]);

                // Create the User with the profile_id
                $user = User::create([
                    'email' => $userEmail,
                    'name' => $item->nama_lengkap ?? $item->email,
                    'password' => Hash::make(Str::random(8)),
                    'email_verified_at' => Carbon::now(),
                    'reactivation' => '1',
                    'role' => RoleEnum::User->value,
                    'profile_status' => '0',
                    'profile_id' => $profile->id,
                    'privacy_policy_agreed_at' => Carbon::now(),
                ]);

                Log::info("Created new user with profile during migration: {$user->email}");
                return $user;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error (MySQL error code 1062)
            if ($e->errorInfo[1] === 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                Log::warning("Duplicate entry detected for email: {$userEmail}. Attempting to fetch existing user.", [
                    'source_id' => $item->source_id,
                    'error' => $e->getMessage()
                ]);

                // Race condition occurred - another process created the user
                // Try to fetch the user that was just created by another process
                $user = User::where('email', $userEmail)->first();

                if ($user) {
                    Log::info("Successfully recovered from duplicate entry - found user: {$user->email}");
                    return $user;
                }

                // If still not found, throw exception
                throw new \Exception("Failed to find or create user with email: {$userEmail} after duplicate entry error.");
            }

            // Re-throw if it's a different database error
            throw $e;
        }
    }

    private function migrateFiles(StagingSyncSuratKuasaModel $item, string $uploadBaseDir): array
    {
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
                $filePaths[$field] = $this->moveAndEncryptFile($sourcePath, $destinationPath);
            } else {
                $filePaths[$field] = null;
            }
        }
        return $filePaths;
    }

    private function createOrUpdatePendaftaran(StagingSyncSuratKuasaModel $item, User $user, array $filePaths, ?string $newStatus): PendaftaranSuratKuasaModel
    {
        return PendaftaranSuratKuasaModel::create(
            [
                'id_daftar' => '#' . $this->infoApp->kode_dipa . '-' . Str::upper(Str::random(3)) . Str::numbers(3),
                'tanggal_daftar' => Carbon::parse($item->tanggal_daftar)->format('Y-m-d') ?? Carbon::now(),
                'migrated_from_id' => $item->source_id,
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
                'user_id' => $user->id,
                'perihal' => $item->perihal,
                'created_at' => Carbon::parse($item->tanggal_daftar)->format('Y-m-d H:i:s') ?? Carbon::now(),
                'updated_at' => Carbon::parse($item->tanggal_daftar)->format('Y-m-d H:i:s') ?? Carbon::now(),
            ]
        );
    }

    private function createPihak(int $pendaftaranId, string $jenis, ?string $jsonNama, ?string $jsonNik, ?string $jsonPekerjaan, ?string $jsonAlamat): void
    {
        $namaPihak = $this->parseJsonArray($jsonNama);
        $nikPihak = $this->parseJsonArray($jsonNik);
        $pekerjaanPihak = $this->parseJsonArray($jsonPekerjaan);
        $alamatPihak = $this->parseJsonArray($jsonAlamat);

        foreach ($namaPihak as $idx => $nama) {
            PihakSuratKuasaModel::create([
                'surat_kuasa_id' => $pendaftaranId,
                'nik' => $nikPihak[$idx] ?? '-',
                'nama' => $nama,
                'pekerjaan' => $pekerjaanPihak[$idx] ?? '-',
                'alamat' => $alamatPihak[$idx] ?? '-',
                'jenis' => $jenis,
            ]);
        }
    }

    private function createPembayaran(StagingSyncSuratKuasaModel $item, int $pendaftaranId, int $userId, ?string $buktiPath): void
    {
        if ($item->tanggal_bayar && $buktiPath) {
            PembayaranSuratKuasaModel::updateOrCreate(
                ['surat_kuasa_id' => $pendaftaranId],
                [
                    'tanggal_pembayaran' => Carbon::parse($item->tanggal_bayar)->format('Y-m-d'),
                    'jenis_pembayaran' => 'Migrasi Data',
                    'bukti_pembayaran' => $buktiPath,
                    'user_payment_id' => $userId,
                    'created_at' => Carbon::parse($item->tanggal_bayar)->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse($item->tanggal_bayar)->format('Y-m-d H:i:s'),
                ]
            );
        }
    }

    private function createRegisterAndDispatchBarcode(StagingSyncSuratKuasaModel $item, int $pendaftaranId, ?int $authId): void
    {
        $panitera = PaniteraModel::where('nama', $item->panitera)->first();

        $register = RegisterSuratKuasaModel::updateOrCreate(
            ['surat_kuasa_id' => $pendaftaranId],
            [
                'uuid' => Str::uuid(),
                'tanggal_register' => Carbon::parse($item->tanggal_disetujui)->format('Y-m-d'),
                'nomor_surat_kuasa' => $item->nomor_surat_kuasa,
                'approval_id' => $authId,
                'panitera_id' => $panitera->id ?? null,
                'path_file' => '',
                'created_at' => Carbon::parse($item->tanggal_disetujui)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($item->tanggal_disetujui)->format('Y-m-d H:i:s'),
            ]
        );

        if ($register->wasRecentlyCreated || empty($register->path_file)) {
            // Dispatch as a background job, NOT synchronously
            GenerateBarcodeSuratKuasaPDF::dispatch($register);
        }
    }
}
