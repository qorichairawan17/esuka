<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile\ProfileModel;
use App\Models\Pengaturan\AplikasiModel;
use App\Models\Pengguna\PaniteraModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $profile = ProfileModel::create([
            'nama_depan' => 'Qori',
            'nama_belakang' => 'Chairawan',
            'tanggal_lahir' => '2000-07-17',
            'jenis_kelamin' => 'Laki-laki',
            'kontak' => '082366025464',
            'alamat' => 'Jalan Jenderal Sudirman No. 58 Lubuk Pakam',
        ]);

        User::create([
            'name' => 'Qori Chairawan',
            'email' => 'qorichairawan17@gmail.com',
            'password' => 'qori',
            'role' => \App\Enum\RoleEnum::Superadmin->value,
            'block' => '0',
            'profile_id' => $profile->id,
            'profile_status' => '1'
        ]);

        AplikasiModel::create([
            'pengadilan_tinggi' => 'Pengadilan Tinggi Medan',
            'pengadilan_negeri' => 'Pengadilan Negeri Lubuk Pakam',
            'kode_dipa' => '400395',
            'kode_surat_kuasa' => '#NOMOR/W2-U4/SK/#BULAN/#TAHUN/PN Lbp',
            'provinsi' => 'Sumatera Utara',
            'kabupaten' => 'Deli Serdang',
            'kode_pos' => '20517',
            'alamat' => 'Jalan Jenderal Sudirman No. 58 Lubuk Pakam',
            'website' => 'https://pn-lubukpakam.go.id/',
            'facebook' => 'https://pn-lubukpakam.go.id/',
            'instagram' => 'https://pn-lubukpakam.go.id/',
            'youtube' => 'https://pn-lubukpakam.go.id/',
            'kontak' => '08238827272',
            'email' => 'pnlubukpakam@yahoo.co.id',
            'maintance' => '0',
        ]);

        PaniteraModel::insert([
            [
                'nip' => '121441',
                'nama' => 'Syawal Aswad Siregar, S.H.,M.Hum',
                'jabatan' => 'Panitera',
                'status' => \App\Enum\StatusPaniteraEnum::NonPlh->value,
                'aktif' => '1',
                'created_by' => '1'
            ],
            [
                'nip' => '121442',
                'nama' => 'Dedy Anthony, SH,MH',
                'jabatan' => 'Panitera Muda Pidana',
                'status' => \App\Enum\StatusPaniteraEnum::Plh->value,
                'aktif' => '1',
                'created_by' => '1'
            ]
        ]);
    }
}
