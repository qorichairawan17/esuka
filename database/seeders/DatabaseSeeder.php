<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile\ProfileModel;
use App\Models\Pengaturan\AplikasiModel;

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
        ]);

        User::create([
            'name' => 'Qori Chairawan',
            'email' => 'qorichairawan17@gmail.com',
            'password' => 'qori',
            'role' => \App\Enum\RoleEnum::Superadmin->value,
            'block' => '0',
            'profile_id' => $profile->id,
            'profile_status' => '0'
        ]);

        AplikasiModel::create([
            'pengadilan_tinggi' => 'Pengadilan Tinggi Medan',
            'pengadilan_negeri' => 'Pengadilan Negeri Lubuk Pakam',
            'kode_dipa' => '400395',
            'provinsi' => 'Sumatera Utara',
            'kabupaten' => 'Deli Serdang',
            'kode_pos' => '20517',
            'alamat' => 'Jalan Jenderal Sudirman No. 58 Lubuk Pakam',
            'website' => 'https://pn-lubukpakam.go.id/',
            'facebook' => '#',
            'instagram' => '#',
            'youtube' => '#',
            'kontak' => '08238827272',
            'email' => 'pnlubukpakam@yahoo.co.id',
            'logo' => 'logo.png',
            'maintance' => '0',
        ]);
    }
}
