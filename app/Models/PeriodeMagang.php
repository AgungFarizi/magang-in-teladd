<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodeMagang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'periode_magang';

    protected $fillable = [
        'nama_periode',
        'tanggal_buka_pendaftaran',
        'tanggal_tutup_pendaftaran',
        'tanggal_mulai_magang',
        'tanggal_selesai_magang',
        'kuota_total',
        'kuota_per_divisi',
        'syarat_dokumen',
        'deskripsi',
        'status',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_buka_pendaftaran' => 'date',
            'tanggal_tutup_pendaftaran' => 'date',
            'tanggal_mulai_magang' => 'date',
            'tanggal_selesai_magang' => 'date',
        ];
    }

    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }

    public function proposal()
    {
        return $this->hasMany(Proposal::class, 'periode_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where('tanggal_buka_pendaftaran', '<=', now())
            ->where('tanggal_tutup_pendaftaran', '>=', now());
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif'
            && now()->between($this->tanggal_buka_pendaftaran, $this->tanggal_tutup_pendaftaran);
    }

    public function getTotalDaftarAttribute(): int
    {
        return $this->proposal()->whereNotIn('status', ['draft', 'dibatalkan'])->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'ditutup' => 'Ditutup',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }
}
