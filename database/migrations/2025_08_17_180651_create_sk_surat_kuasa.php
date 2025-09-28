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
        Schema::create('sk_pendaftaran_surat_kuasa', function (Blueprint $table) {
            $table->id();
            $table->string('id_daftar')->index();
            $table->date('tanggal_daftar');
            $table->text('perihal');
            $table->enum('jenis_surat', ['Pidana', 'Perdata']);
            $table->string('klasifikasi');
            $table->text('edoc_kartu_tanda_penduduk');
            $table->text('edoc_kartu_tanda_anggota')->nullable()->comment('Untuk Advokat');
            $table->text('edoc_kartu_tanda_pegawai')->nullable()->comment('Untuk Non Advokat');
            $table->text('edoc_berita_acara_sumpah')->nullable()->comment('Untuk Advokat');;
            $table->text('edoc_surat_tugas')->nullable()->comment('Untuk Non Advokat');;
            $table->text('edoc_surat_kuasa');
            $table->string('tahapan');
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('pemohon')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });

        Schema::create('sk_register_surat_kuasa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_kuasa_id');
            $table->uuid('uuid');
            $table->date('tanggal_register');
            $table->string('nomor_surat_kuasa');
            $table->unsignedBigInteger('approval_id');
            $table->unsignedBigInteger('panitera_id');
            $table->text('path_file');
            $table->timestamps();

            $table->foreign('surat_kuasa_id')->references('id')->on('sk_pendaftaran_surat_kuasa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('approval_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('panitera_id')->references('id')->on('sk_panitera')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sk_pembayaran_surat_kuasa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_kuasa_id');
            $table->date('tanggal_pembayaran');
            $table->string('jenis_pembayaran');
            $table->text('bukti_pembayaran');
            $table->unsignedBigInteger('user_payment_id');
            $table->timestamps();

            $table->foreign('surat_kuasa_id')->references('id')->on('sk_pendaftaran_surat_kuasa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_payment_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sk_pihak_surat_kuasa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_kuasa_id');
            $table->text('nik');
            $table->string('nama');
            $table->string('pekerjaan');
            $table->text('alamat');
            $table->enum('jenis', ['Penerima', 'Pemberi']);
            $table->timestamps();

            $table->foreign('surat_kuasa_id')->references('id')->on('sk_pendaftaran_surat_kuasa')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_surat_kuasa');
    }
};
