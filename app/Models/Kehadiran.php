<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'kehadiran';

    protected $fillable = [
        'proposal_id',
        'mahasiswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'lokasi_masuk',
        'lokasi_keluar',
        'foto_masuk',
        'foto_keluar',
        'status_kehadiran',
        'keterangan',
        'status_verifikasi',
        'diverifikasi_oleh',
        'tgl_verifikasi',
        'catatan_pembimbing',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tgl_verifikasi' => 'datetime',
        ];
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Pengguna::class, 'mahasiswa_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(Pengguna::class, 'diverifikasi_oleh');
    }

    public function logHarian()
    {
        return $this->hasOne(LogHarian::class, 'kehadiran_id');
    }

    public function getStatusKehadiranLabelAttribute(): string
    {
        return match($this->status_kehadiran) {
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpha' => 'Alpha',
            'libur' => 'Libur',
            default => $this->status_kehadiran,
        };
    }

    public function getStatusVerifikasiLabelAttribute(): string
    {
        return match($this->status_verifikasi) {
            'pending' => 'Menunggu Verifikasi',
            'diverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            default => $this->status_verifikasi,
        };
    }
}
