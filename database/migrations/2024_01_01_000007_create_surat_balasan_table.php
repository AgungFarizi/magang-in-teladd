<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_balasan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->enum('jenis', ['penerimaan', 'penolakan']);
            $table->string('perihal');
            $table->text('isi_surat');
            $table->string('file_surat')->nullable()->comment('Path PDF surat');
            $table->date('tanggal_surat');
            $table->foreignId('dibuat_oleh')->constrained('pengguna')->cascadeOnDelete();
            $table->timestamp('dikirim_pada')->nullable();
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index('proposal_id');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_balasan');
    }
};
