<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staging_sync_surat_kuasa', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('tanggal_daftar')->nullable();
            $table->string('email');
            $table->string('nama_lengkap')->nullable();
            $table->text('perihal');
            $table->string('jenis_surat');
            $table->text('edoc_kartu_tanda_penduduk')->nullable();
            $table->text('edoc_kartu_tanda_anggota')->nullable();
            $table->text('edoc_kartu_tanda_pegawai')->nullable();
            $table->text('edoc_berita_acara_sumpah')->nullable();
            $table->text('edoc_surat_tugas')->nullable();
            $table->text('edoc_surat_kuasa')->nullable();
            $table->text('id_pemberi');
            $table->text('nik_pemberi');
            $table->text('nama_pemberi');
            $table->text('pekerjaan_pemberi')->nullable();
            $table->text('alamat_pemberi')->nullable();
            $table->text('id_penerima');
            $table->text('nik_penerima');
            $table->text('nama_penerima');
            $table->text('pekerjaan_penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->text('bukti_pembayaran')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('panitera')->nullable();
            $table->string('nomor_surat_kuasa')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staging_sync_surat_kuasa');
    }
};
