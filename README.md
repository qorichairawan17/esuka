<div align="center">

# ⚖️ Elektronik Surat Kuasa (e-SuKa)

![e-Suka Landing Page](public/icons/horizontal-e-suka.png)

</div>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

**e-SuKa** adalah aplikasi web inovatif yang dikembangkan oleh **Pengadilan Negeri Lubuk Pakam** untuk memodernisasi proses pendaftaran surat kuasa. Dengan asas **Mudah, Cepat, dan Biaya Ringan**, aplikasi ini memungkinkan advokat dan masyarakat untuk mendaftarkan surat kuasa secara elektronik dari mana saja dan kapan saja.

🏆 Aplikasi ini telah meraih penghargaan dari **Direktorat Jenderal Badan Peradilan Umum** sebagai salah satu aplikasi terbaik dalam kategori **"Penerapan Aplikasi Pelayanan Publik"** pada tahun 2022.

---

### 📜 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Panduan Instalasi](#-panduan-instalasi)
- [Alur Penggunaan](#-alur-penggunaan)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)
- [Ucapan Terima Kasih](#-ucapan-terima-kasih)

---

### 🌟 Tentang Proyek

Proyek e-SuKa lahir dari kebutuhan untuk mengatasi beban administrasi yang tinggi dalam pelayanan legalisasi pendaftaran surat kuasa di Pengadilan Negeri Lubuk Pakam. Proses manual yang memakan waktu dan mengharuskan kehadiran fisik kini ditransformasikan menjadi alur digital yang efisien.

Tujuan utama dari aplikasi ini adalah:
- **Memudahkan** pengguna dalam mendaftarkan surat kuasa.
- **Mempercepat** proses verifikasi dan legalisasi oleh petugas.
- **Meringankan** biaya dan waktu yang dibutuhkan oleh para pencari keadilan.
- **Meningkatkan** transparansi dan akuntabilitas dalam pelayanan publik.

---

### ✨ Fitur Utama

- **Pendaftaran Online**: Pengguna dapat mengajukan pendaftaran surat kuasa baru dengan mengisi formulir dan mengunggah dokumen pendukung secara online.
- **Otentikasi Pengguna**: Sistem login yang aman dengan registrasi mandiri, aktivasi akun melalui email, dan opsi login menggunakan **Google Socialite**.
- **Manajemen Profil Lengkap**: Pengguna dapat melengkapi dan memperbarui data profil, foto, serta mengubah password.
- **Pembayaran Digital**: Integrasi dengan sistem pembayaran **QRIS** untuk biaya pendaftaran yang transparan dan mudah.
- **Verifikasi oleh Petugas**: Panel administrasi khusus bagi petugas untuk me-review, memverifikasi, menyetujui, atau menolak pengajuan surat kuasa.
- **Notifikasi Status**: Pengguna mendapatkan informasi real-time mengenai status pengajuan mereka (Menunggu, Disetujui, Ditolak).
- **Cetak Barcode**: Setelah disetujui, sistem akan menghasilkan barcode pendaftaran elektronik yang sah untuk digunakan.
- **Audit Trail**: Pencatatan setiap aktivitas penting pengguna untuk keamanan dan jejak audit.
- **Testimoni Pengguna**: Fitur bagi pengguna untuk memberikan ulasan dan rating terhadap layanan.
- **Manajemen Konten Dinamis**: Halaman depan yang informatif dengan data pejabat struktural dan informasi aplikasi yang dapat dikelola oleh admin.

---

### 🚀 Teknologi yang Digunakan

Proyek ini dibangun menggunakan tumpukan teknologi modern dan andal:

| Kategori | Teknologi |
| :--- | :--- |
| **Framework Backend** | [Laravel 12](https://laravel.com/) |
| **Bahasa Pemrograman** | [PHP 8.2](https://www.php.net/) |
| **Database** | [MySQL](https://www.mysql.com/) |
| **Frontend Bundler** | [Vite](https://vitejs.dev/) |
| **CSS Framework** | [Tailwind CSS](https://tailwindcss.com/) |
| **JavaScript** | [jQuery](https://jquery.com/) & AJAX |
| **Paket Utama** | |
| &nbsp; &nbsp; ↳ Tabel Data | [Yajra DataTables](https://yajrabox.com/docs/laravel-datatables/master) |
| &nbsp; &nbsp; ↳ Otentikasi Sosial | [Laravel Socialite](https://laravel.com/docs/11.x/socialite) |
| &nbsp; &nbsp; ↳ Keamanan Form | [Mews Captcha](https://github.com/mewebstudio/captcha) |
| &nbsp; &nbsp; ↳ Manipulasi Gambar | [Intervention Image](https://image.intervention.io/) |

---

### 🛠️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- Database (MySQL/MariaDB)

#### Langkah-langkah Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/esuka.git
    cd esuka
    ```

2.  **Install Dependensi PHP**
    ```bash
    composer install
    ```

3.  **Install Dependensi JavaScript**
    ```bash
    npm install
    ```

4.  **Konfigurasi Lingkungan (.env)**
    - Salin file `.env.example` menjadi `.env`.
      ```bash
      cp .env.example .env
      ```
    - Buka file `.env` dan atur koneksi database Anda (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    - Atur `APP_URL` sesuai dengan URL lokal Anda.

5.  **Generate Kunci Aplikasi**
    ```bash
    php artisan key:generate
    ```

6.  **Jalankan Migrasi & Seeder**
    ```bash
    php artisan migrate --seed
    ```

7.  **Buat Symbolic Link**
    ```bash
    php artisan storage:link
    ```

8.  **Build Aset Frontend (untuk produksi)**
    ```bash
    npm run build
    ```

9.  **Jalankan Server Development**
    - Untuk menjalankan server dan Vite secara bersamaan, Anda bisa menggunakan script `dev` yang sudah disediakan di `composer.json`.
      ```bash
      composer dev
      ```
    - Atau jalankan secara terpisah:
      ```bash
      # Di terminal 1
      php artisan serve

      # Di terminal 2
      npm run dev
      ```

Aplikasi sekarang seharusnya sudah bisa diakses di `http://127.0.0.1:8000`.

---

### ⚙️ Alur Penggunaan

#### 👤 Alur Pengguna
1.  **Registrasi**: Membuat akun baru melalui form pendaftaran.
2.  **Aktivasi Akun**: Mengklik link aktivasi yang dikirimkan ke email.
3.  **Login**: Masuk ke sistem menggunakan email dan password atau akun Google.
4.  **Lengkapi Profil**: Mengisi data diri lengkap sebelum dapat mengajukan surat kuasa.
5.  **Ajukan Surat Kuasa**: Mengisi detail surat kuasa dan mengunggah dokumen yang diperlukan.
6.  **Lakukan Pembayaran**: Membayar biaya pendaftaran melalui QRIS dan mengunggah bukti bayar.
7.  **Tunggu Verifikasi**: Menunggu petugas memverifikasi data dan pembayaran.
8.  **Unduh Barcode**: Jika disetujui, unduh barcode pendaftaran elektronik. Jika ditolak, perbaiki data sesuai catatan dari petugas.

#### 👮 Alur Admin/Petugas
1.  **Login**: Masuk ke panel administrator.
2.  **Lihat Pengajuan**: Meninjau daftar surat kuasa yang masuk.
3.  **Verifikasi**: Memeriksa kelengkapan dan keabsahan dokumen serta bukti pembayaran.
4.  **Setujui/Tolak**: Memberikan status persetujuan atau penolakan dengan menyertakan alasan jika ditolak.

---

### 🤝 Kontribusi & Penggunaan

Proyek ini adalah perangkat lunak dengan hak milik **(proprietary software)**. Penggunaan, modifikasi, dan distribusi kode sumber hanya diizinkan dengan persetujuan tertulis dari pemilik hak cipta. Proyek ini tidak menerima kontribusi eksternal.

---

### 📄 Lisensi

Hak Cipta © 2021 Pengadilan Negeri Lubuk Pakam & Qori Chairawan. Semua Hak Dilindungi.

---

### 🙏 Ucapan Terima Kasih

- **Pengadilan Negeri Lubuk Pakam** - Atas inisiasi dan dukungan penuh terhadap proyek ini.
- **Direktorat Jenderal Badan Peradilan Umum** - Atas pengakuan dan penghargaan yang diberikan.
- **Qori Chairawan** - Selaku Developer Elektronik Surat Kuasa.

---
