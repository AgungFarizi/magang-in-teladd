<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', [
                'proposal_diajukan',
                'proposal_diteruskan',
                'proposal_diterima',
                'proposal_ditolak',
                'surat_balasan',
                'kehadiran_diverifikasi',
                'log_diverifikasi',
                'log_revisi',
                'sistem',
                'info'
            ])->default('info');
            $table->string('url')->nullable()->comment('Link terkait notifikasi');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->nullableMorphs('notifiable');
            $table->timestamps();

            $table->index(['pengguna_id', 'sudah_dibaca']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
