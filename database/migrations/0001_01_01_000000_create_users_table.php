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
        Schema::create('sk_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->text('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Perempuan', 'Laki-Laki'])->nullable();
            $table->text('kontak')->nullable();
            $table->text('alamat')->nullable();
            $table->text('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role');
            $table->enum('block', ['1', '0'])->default('0');
            $table->enum('reactivation', ['1', '0'])->default('0');
            $table->unsignedBigInteger('profile_id');
            $table->enum('profile_status', ['1', '0'])->default('0')->comment('1 = Verified, 0 = Unverified');
            $table->timestamp('privacy_policy_agreed_at')->nullable();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('sk_user_profiles')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_user_profiles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
