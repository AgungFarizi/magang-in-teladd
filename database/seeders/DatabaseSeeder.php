<?php

namespace Database\Seeders;

use App\Models\AksesTokenAdmin;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Manager (Super Admin) ─────────────────────────────────────
        $manager = Pengguna::create([
            'nama_lengkap' => 'Admin Manager',
            'email' => 'manager@tellinter.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'no_telepon' => '081234567890',
            'is_active' => true,
        ]);

        // ── Manager Departemen ────────────────────────────────────────
        $managerIt = Pengguna::create([
            'nama_lengkap' => 'Manager Departemen IT',
            'email' => 'manager.it@tellinter.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'manager_departemen',
            'divisi' => 'IT',
            'no_telepon' => '081234567891',
            'is_active' => true,
        ]);

        Pengguna::create([
            'nama_lengkap' => 'Manager Departemen Keuangan',
            'email' => 'manager.keuangan@tellinter.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'manager_departemen',
            'divisi' => 'Keuangan',
            'no_telepon' => '081234567892',
            'is_active' => true,
        ]);

        // ── Operator ─────────────────────────────────────────────────
        $operator = Pengguna::create([
            'nama_lengkap' => 'Operator Utama',
            'email' => 'operator@tellinter.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'divisi' => 'Umum',
            'no_telepon' => '081234567893',
            'is_active' => true,
        ]);

        // ── Pembimbing ────────────────────────────────────────────────
        $pembimbing = Pengguna::create([
            'nama_lengkap' => 'Pembimbing IT',
            'email' => 'pembimbing.it@tellinter.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'pembimbing_lapang',
            'divisi' => 'IT',
            'no_telepon' => '081234567894',
            'is_active' => true,
        ]);

        // ── Mahasiswa Demo ────────────────────────────────────────────
        Pengguna::create([
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@mahasiswa.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'nim' => '20210001',
            'no_telepon' => '081234567895',
            'institusi' => 'Universitas Indonesia',
            'jurusan' => 'Teknik Informatika',
            'is_active' => true,
        ]);

        Pengguna::create([
            'nama_lengkap' => 'Siti Rahayu',
            'email' => 'siti@mahasiswa.ac.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'nim' => '20210002',
            'no_telepon' => '081234567896',
            'institusi' => 'Institut Teknologi Bandung',
            'jurusan' => 'Sistem Informasi',
            'is_active' => true,
        ]);

        // ── Periode Magang ────────────────────────────────────────────
        \App\Models\PeriodeMagang::create([
            'nama_periode' => 'Magang Semester Genap 2024/2025',
            'tanggal_buka_pendaftaran' => now()->subDays(5),
            'tanggal_tutup_pendaftaran' => now()->addDays(30),
            'tanggal_mulai_magang' => now()->addDays(45),
            'tanggal_selesai_magang' => now()->addDays(135),
            'kuota_total' => 50,
            'kuota_per_divisi' => 10,
            'syarat_dokumen' => "- Surat pengantar dari kampus\n- Transkrip nilai terbaru\n- CV/Resume",
            'deskripsi' => 'Program magang semester genap tahun akademik 2024/2025.',
            'status' => 'aktif',
            'dibuat_oleh' => $operator->id,
        ]);

        // ── Token Demo ────────────────────────────────────────────────
        AksesTokenAdmin::create([
            'token' => 'DEMO-OPER-ATOR-2024',
            'untuk_role' => 'operator',
            'divisi' => 'IT',
            'keterangan' => 'Token demo untuk operator divisi IT',
            'is_active' => true,
            'dibuat_oleh' => $manager->id,
        ]);

        AksesTokenAdmin::create([
            'token' => 'DEMO-PMBG-LAPG-2024',
            'untuk_role' => 'pembimbing_lapang',
            'divisi' => 'IT',
            'keterangan' => 'Token demo untuk pembimbing IT',
            'is_active' => true,
            'dibuat_oleh' => $manager->id,
        ]);

        echo "\n✅ TELLINTER Database Seeded Successfully!\n";
        echo "────────────────────────────────────────\n";
        echo "👤 Akun Demo:\n";
        echo "  Manager:       manager@tellinter.ac.id      / password123\n";
        echo "  Manajer Dep.:  manager.it@tellinter.ac.id   / password123\n";
        echo "  Operator:      operator@tellinter.ac.id     / password123\n";
        echo "  Pembimbing:    pembimbing.it@tellinter.ac.id / password123\n";
        echo "  Mahasiswa:     budi@mahasiswa.ac.id         / password123\n";
        echo "────────────────────────────────────────\n";
        echo "🔑 Token Admin Demo:\n";
        echo "  Operator:    DEMO-OPER-ATOR-2024\n";
        echo "  Pembimbing:  DEMO-PMBG-LAPG-2024\n";
    }
}
