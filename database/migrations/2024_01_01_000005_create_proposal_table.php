<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_proposal')->unique()->comment('Auto-generated: PROP-YYYY-XXXX');
            $table->foreignId('periode_id')->constrained('periode_magang')->cascadeOnDelete();
            $table->foreignId('pengaju_id')->constrained('pengguna')->cascadeOnDelete()->comment('Ketua kelompok / mahasiswa pengaju');
            $table->string('divisi_tujuan')->comment('Divisi yang dituju');
            $table->string('judul_proposal');
            $table->text('latar_belakang');
            $table->text('tujuan');
            $table->date('tanggal_mulai_diinginkan');
            $table->date('tanggal_selesai_diinginkan');
            $table->string('file_proposal')->comment('Path PDF proposal');
            $table->string('file_surat_pengantar')->nullable()->comment('Path PDF surat pengantar kampus');
            $table->string('file_transkrip')->nullable()->comment('Path PDF transkrip nilai');
            $table->string('file_cv')->nullable();

            // Status workflow
            $table->enum('status', [
                'draft',
                'diajukan',
                'review_operator',
                'diteruskan_manager',
                'review_manager_dep',
                'disetujui_manager_dep',
                'ditolak_manager_dep',
                'review_manager',
                'diterima',
                'ditolak',
                'aktif',
                'selesai',
                'dibatalkan'
            ])->default('draft');

            // Review Operator
            $table->foreignId('operator_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('tgl_review_operator')->nullable();
            $table->text('catatan_operator')->nullable();

            // Review Manager Departemen
            $table->foreignId('manager_dep_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('tgl_review_manager_dep')->nullable();
            $table->text('catatan_manager_dep')->nullable();

            // Review Manager
            $table->foreignId('manager_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('tgl_review_manager')->nullable();
            $table->text('catatan_manager')->nullable();

            // Pembimbing yang ditugaskan
            $table->foreignId('pembimbing_id')->nullable()->constrained('pengguna')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('divisi_tujuan');
            $table->index('nomor_proposal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal');
    }
};
