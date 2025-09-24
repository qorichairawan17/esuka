<?php

namespace App\Services;

use App\Enum\SuratKuasaEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Models\Sync\StagingSyncSuratKuasaModel;

class SyncService
{
    private $apiUrl, $apiToken;
    public function __construct()
    {
        $this->apiUrl = config('services.sync_api.url');
        $this->apiToken = config('services.sync_api.token');
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
                // The unique key should be based on the source table's primary key (sk.id)
                // to ensure no data is overwritten.
                StagingSyncSuratKuasaModel::updateOrCreate(
                    ['source_id' => $item->surat_kuasa_id], // Kunci unik berdasarkan ID dari database sumber
                    [
                        'user_id' => $item->user_id,
                        // Data untuk diupdate atau dibuat
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
                        'photo_bukti' => $item->photo_bukti,
                        'status' => $item->status,
                        'keterangan' => $item->keterangan,
                        'panitera' => $item->panitera,
                        'nomor_surat_kuasa' => $item->nomor_surat_kuasa,
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
        // Query ini menggunakan GROUP_CONCAT untuk menggabungkan data dari banyak pihak menjadi satu string JSON array.
        // GROUP BY memastikan setiap surat kuasa hanya menghasilkan satu baris.
        $baseQuery = "
            SELECT
                sk.id AS surat_kuasa_id,
                sk.id_pengguna AS user_id,
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
                byr.photo_bukti,
                byr.pesan AS keterangan,
                statusbyr.keterangan AS status,
                register.panitera,
                register.no_pendaftaran AS nomor_surat_kuasa
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

        $groupByClause = " GROUP BY sk.id, u.id, u.email, p.nama_lengkap, sk.perihal, sk.jenis, sk.ktp, sk.kartu_advokat, sk.kartu_tanda_pegawai, sk.ba_sumpah, sk.surat_tugas, sk.surat_kuasa, byr.photo_bukti, byr.pesan, statusbyr.keterangan, register.panitera, register.no_pendaftaran";

        return $baseQuery . $whereClause . $groupByClause;
    }

    public function fetchEdoc($klasifikasi)
    {
        // Ensure API URL and Token are configured
        if (empty($this->apiUrl) || empty($this->apiToken)) {
            Log::error('API URL or Token is not configured for sync.', [
                'apiUrl' => $this->apiUrl,
                'apiToken' => $this->apiToken ? 'configured' : 'not configured'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'API URL or Token is not configured. Please check your services.php config and .env file.'
            ], 500);
        }

        Log::info('Starting fetch edoc', ['api_url' => $this->apiUrl]);

        DB::beginTransaction();
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ])->get($this->apiUrl);

            if ($response->successful()) {
                $apiData = $response->json();
                if (!isset($apiData['data']) || !is_array($apiData['data'])) {
                    Log::warning('API response does not contain a valid "data" array.', ['response' => $apiData]);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to sync data: Invalid API response format. Expected "data" array.'
                    ], 500);
                }

                $insertedCount = 0;
                foreach ($apiData['data'] as $item) {

                    $insertedCount++;
                };
                DB::commit();
                Log::info('Successfully synchronized Surat Kuasa data.', ['inserted_count' => $insertedCount]);
                return response()->json([
                    'success' => true,
                    'message' => "Successfully synchronized {$insertedCount} Surat Kuasa records."
                ]);
            } else {
                DB::rollBack();
                Log::error('Failed to fetch data from API.', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from the external API. Status: ' . $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during Surat Kuasa data synchronization.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during synchronization: ' . $e->getMessage()
            ], 500);
        }
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
}
