<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['manager', 'manager_departemen', 'operator', 'pembimbing_lapang', 'mahasiswa']);
            $table->string('nim')->nullable()->unique()->comment('Khusus mahasiswa');
            $table->string('no_telepon', 20)->nullable();
            $table->string('institusi')->nullable()->comment('Universitas/instansi');
            $table->string('jurusan')->nullable();
            $table->string('divisi')->nullable()->comment('Divisi untuk manager_dep, operator, pembimbing');
            $table->string('foto_profil')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('divisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
