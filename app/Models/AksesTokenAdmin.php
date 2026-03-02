<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AksesTokenAdmin extends Model
{
    protected $table = 'akses_token_admin';

    protected $fillable = [
        'token',
        'untuk_role',
        'divisi',
        'keterangan',
        'is_used',
        'digunakan_oleh',
        'digunakan_pada',
        'is_active',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'is_active' => 'boolean',
            'digunakan_pada' => 'datetime',
        ];
    }

    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }

    public function penggunaToken()
    {
        return $this->belongsTo(Pengguna::class, 'digunakan_oleh');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->where('is_used', false);
    }

    public static function generate(string $role, ?string $divisi, string $keterangan, int $dibuatOleh): self
    {
        return static::create([
            'token' => strtoupper(Str::random(8) . '-' . Str::random(8) . '-' . Str::random(8)),
            'untuk_role' => $role,
            'divisi' => $divisi,
            'keterangan' => $keterangan,
            'dibuat_oleh' => $dibuatOleh,
        ]);
    }

    public function gunakanToken(int $penggunaId): bool
    {
        return $this->update([
            'is_used' => true,
            'digunakan_oleh' => $penggunaId,
            'digunakan_pada' => now(),
        ]);
    }
}
