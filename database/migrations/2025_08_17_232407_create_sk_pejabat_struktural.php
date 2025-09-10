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
        Schema::create('sk_pejabat_struktural', function (Blueprint $table) {
            $table->id();
            $table->string('ketua');
            $table->string('foto_ketua');
            $table->string('wakil_ketua');
            $table->string('foto_wakil_ketua');
            $table->string('panitera');
            $table->string('foto_panitera');
            $table->string('sekretaris');
            $table->string('foto_sekretaris');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_pejabat_struktural');
    }
};
