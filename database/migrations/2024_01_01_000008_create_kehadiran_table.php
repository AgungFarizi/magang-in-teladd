<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('pengguna')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('lokasi_masuk')->nullable()->comment('Koordinat GPS atau alamat');
            $table->string('lokasi_keluar')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'sakit', 'izin', 'alpha', 'libur'])->default('hadir');
            $table->text('keterangan')->nullable();

            // Verifikasi pembimbing
            $table->enum('status_verifikasi', ['pending', 'diverifikasi', 'ditolak'])->default('pending');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('tgl_verifikasi')->nullable();
            $table->text('catatan_pembimbing')->nullable();

            $table->timestamps();

            $table->unique(['proposal_id', 'mahasiswa_id', 'tanggal']);
            $table->index(['mahasiswa_id', 'tanggal']);
            $table->index('status_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
