<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proposal';

    protected $fillable = [
        'nomor_proposal',
        'periode_id',
        'pengaju_id',
        'divisi_tujuan',
        'judul_proposal',
        'latar_belakang',
        'tujuan',
        'tanggal_mulai_diinginkan',
        'tanggal_selesai_diinginkan',
        'file_proposal',
        'file_surat_pengantar',
        'file_transkrip',
        'file_cv',
        'status',
        'operator_id',
        'tgl_review_operator',
        'catatan_operator',
        'manager_dep_id',
        'tgl_review_manager_dep',
        'catatan_manager_dep',
        'manager_id',
        'tgl_review_manager',
        'catatan_manager',
        'pembimbing_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai_diinginkan' => 'date',
            'tanggal_selesai_diinginkan' => 'date',
            'tgl_review_operator' => 'datetime',
            'tgl_review_manager_dep' => 'datetime',
            'tgl_review_manager' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function periode()
    {
        return $this->belongsTo(PeriodeMagang::class, 'periode_id');
    }

    public function pengaju()
    {
        return $this->belongsTo(Pengguna::class, 'pengaju_id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaProposal::class, 'proposal_id');
    }

    public function operator()
    {
        return $this->belongsTo(Pengguna::class, 'operator_id');
    }

    public function managerDep()
    {
        return $this->belongsTo(Pengguna::class, 'manager_dep_id');
    }

    public function manager()
    {
        return $this->belongsTo(Pengguna::class, 'manager_id');
    }

    public function pembimbing()
    {
        return $this->belongsTo(Pengguna::class, 'pembimbing_id');
    }

    public function suratBalasan()
    {
        return $this->hasOne(SuratBalasan::class, 'proposal_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'proposal_id');
    }

    public function logHarian()
    {
        return $this->hasMany(LogHarian::class, 'proposal_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeByDivisi($query, $divisi)
    {
        return $query->where('divisi_tujuan', $divisi);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['diajukan', 'review_operator', 'diteruskan_manager', 'review_manager_dep', 'review_manager']);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ─── Accessors ───────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'review_operator' => 'Review Operator',
            'diteruskan_manager' => 'Diteruskan ke Manager',
            'review_manager_dep' => 'Review Manager Departemen',
            'disetujui_manager_dep' => 'Disetujui Manager Dep.',
            'ditolak_manager_dep' => 'Ditolak Manager Dep.',
            'review_manager' => 'Review Manager',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'aktif' => 'Sedang Magang',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'diajukan', 'review_operator', 'review_manager_dep', 'review_manager', 'diteruskan_manager' => 'yellow',
            'disetujui_manager_dep' => 'blue',
            'diterima', 'aktif', 'selesai' => 'green',
            'ditolak', 'ditolak_manager_dep', 'dibatalkan' => 'red',
            default => 'gray',
        };
    }

    public function isDiterima(): bool
    {
        return in_array($this->status, ['diterima', 'aktif', 'selesai']);
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // ─── Static ─────────────────────────────────────────────────────
    public static function generateNomor(): string
    {
        $tahun = date('Y');
        $bulan = date('m');
        $urutan = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('PROP-%s%s-%04d', $tahun, $bulan, $urutan);
    }

    // ─── Boot ────────────────────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($proposal) {
            if (empty($proposal->nomor_proposal)) {
                $proposal->nomor_proposal = static::generateNomor();
            }
        });
    }
}
