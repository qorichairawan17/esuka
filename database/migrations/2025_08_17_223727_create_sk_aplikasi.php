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
        Schema::create('sk_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->string('pengadilan_tinggi');
            $table->string('pengadilan_negeri');
            $table->string('kode_dipa');
            $table->string('kode_surat_kuasa');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kode_pos', 10);
            $table->text('alamat');
            $table->string('website');
            $table->string('facebook');
            $table->string('instagram');
            $table->string('youtube');
            $table->string('kontak', 15);
            $table->string('email');
            $table->string('logo')->nullable();
            $table->enum('maintance', ['1', '0'])->default('0')->comment('1 = Yes, 0 = No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_aplikasi');
    }
};
