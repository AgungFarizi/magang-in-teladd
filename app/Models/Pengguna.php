<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'email_verified_at',
        'password',
        'role',
        'nim',
        'no_telepon',
        'institusi',
        'jurusan',
        'divisi',
        'foto_profil',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ─── Role Checks ────────────────────────────────────────────────
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isManagerDepartemen(): bool
    {
        return $this->role === 'manager_departemen';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isPembimbingLapang(): bool
    {
        return $this->role === 'pembimbing_lapang';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['manager', 'manager_departemen', 'operator', 'pembimbing_lapang']);
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function proposalDiajukan()
    {
        return $this->hasMany(Proposal::class, 'pengaju_id');
    }

    public function anggotaProposal()
    {
        return $this->hasMany(AnggotaProposal::class, 'pengguna_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }

    public function logHarian()
    {
        return $this->hasMany(LogHarian::class, 'mahasiswa_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }

    public function notifikasiTidakDibaca()
    {
        return $this->notifikasi()->where('sudah_dibaca', false);
    }

    public function periodeYangDibuat()
    {
        return $this->hasMany(PeriodeMagang::class, 'dibuat_oleh');
    }

    public function mahasiswaBimbingan()
    {
        return $this->hasMany(Proposal::class, 'pembimbing_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeDivisi($query, $divisi)
    {
        return $query->where('divisi', $divisi);
    }

    // ─── Accessors ───────────────────────────────────────────────────
    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil && file_exists(storage_path('app/public/' . $this->foto_profil))) {
            return asset('storage/' . $this->foto_profil);
        }
        return asset('images/default-avatar.png');
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'manager' => 'Manager',
            'manager_departemen' => 'Manager Departemen',
            'operator' => 'Operator',
            'pembimbing_lapang' => 'Pembimbing Lapang',
            'mahasiswa' => 'Mahasiswa',
            default => ucfirst($this->role),
        };
    }
}
