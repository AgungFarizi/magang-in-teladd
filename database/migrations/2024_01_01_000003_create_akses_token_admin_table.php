<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akses_token_admin', function (Blueprint $table) {
            $table->id();
            $table->string('token', 100)->unique();
            $table->enum('untuk_role', ['manager', 'manager_departemen', 'operator', 'pembimbing_lapang']);
            $table->string('divisi')->nullable()->comment('Wajib untuk manager_dep, operator, pembimbing');
            $table->string('keterangan')->nullable();
            $table->boolean('is_used')->default(false);
            $table->foreignId('digunakan_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('digunakan_pada')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('dibuat_oleh')->constrained('pengguna')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['token', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses_token_admin');
    }
};
