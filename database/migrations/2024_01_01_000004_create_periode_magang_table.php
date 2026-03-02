<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_magang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode')->comment('Contoh: Magang Semester Ganjil 2024/2025');
            $table->date('tanggal_buka_pendaftaran');
            $table->date('tanggal_tutup_pendaftaran');
            $table->date('tanggal_mulai_magang');
            $table->date('tanggal_selesai_magang');
            $table->integer('kuota_total')->default(0)->comment('0 = tidak terbatas');
            $table->integer('kuota_per_divisi')->default(0);
            $table->text('syarat_dokumen')->nullable()->comment('Daftar dokumen yang diperlukan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'aktif', 'ditutup', 'selesai'])->default('draft');
            $table->foreignId('dibuat_oleh')->constrained('pengguna')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(
            ['tanggal_buka_pendaftaran', 'tanggal_tutup_pendaftaran'],
            'periode_tanggal_index'
        );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_magang');
    }
};
