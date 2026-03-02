<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kehadiran_id')->constrained('kehadiran')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('judul_aktivitas');
            $table->text('deskripsi_aktivitas');
            $table->enum('kategori', [
                'pembelajaran',
                'proyek',
                'administrasi',
                'presentasi',
                'diskusi',
                'laporan',
                'lainnya'
            ])->default('lainnya');
            $table->integer('durasi_menit')->default(0)->comment('Durasi dalam menit');
            $table->json('file_dokumentasi')->nullable()->comment('Array path file/foto');
            $table->text('kendala')->nullable();
            $table->text('rencana_besok')->nullable();

            // Verifikasi pembimbing
            $table->enum('status_verifikasi', ['pending', 'diverifikasi', 'revisi', 'ditolak'])->default('pending');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('tgl_verifikasi')->nullable();
            $table->text('feedback_pembimbing')->nullable();
            $table->integer('nilai_pembimbing')->nullable()->comment('1-100');

            $table->timestamps();

            $table->unique(['kehadiran_id', 'mahasiswa_id']);
            $table->index(['mahasiswa_id', 'tanggal']);
            $table->index('status_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_harian');
    }
};
