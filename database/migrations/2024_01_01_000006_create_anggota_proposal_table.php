<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();

            // Data anggota (untuk anggota yang belum punya akun)
            $table->string('nama_lengkap');
            $table->string('nim');
            $table->string('email');
            $table->string('no_telepon', 20)->nullable();
            $table->string('jurusan')->nullable();
            $table->string('institusi')->nullable();

            $table->enum('peran', ['ketua', 'anggota'])->default('anggota');
            $table->boolean('is_confirmed')->default(false)->comment('Konfirmasi dari anggota');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['proposal_id', 'nim']);
            $table->index('proposal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_proposal');
    }
};
