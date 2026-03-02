<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogHarian extends Model
{
    use HasFactory;

    protected $table = 'log_harian';

    protected $fillable = [
        'kehadiran_id',
        'mahasiswa_id',
        'proposal_id',
        'tanggal',
        'judul_aktivitas',
        'deskripsi_aktivitas',
        'kategori',
        'durasi_menit',
        'file_dokumentasi',
        'kendala',
        'rencana_besok',
        'status_verifikasi',
        'diverifikasi_oleh',
        'tgl_verifikasi',
        'feedback_pembimbing',
        'nilai_pembimbing',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'file_dokumentasi' => 'array',
            'tgl_verifikasi' => 'datetime',
        ];
    }

    public function kehadiran()
    {
        return $this->belongsTo(Kehadiran::class, 'kehadiran_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Pengguna::class, 'mahasiswa_id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(Pengguna::class, 'diverifikasi_oleh');
    }

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'pembelajaran' => 'Pembelajaran',
            'proyek' => 'Proyek',
            'administrasi' => 'Administrasi',
            'presentasi' => 'Presentasi',
            'diskusi' => 'Diskusi',
            'laporan' => 'Laporan',
            'lainnya' => 'Lainnya',
            default => $this->kategori,
        };
    }

    public function getDurasiFormatAttribute(): string
    {
        $jam = intdiv($this->durasi_menit, 60);
        $menit = $this->durasi_menit % 60;
        if ($jam > 0) {
            return "{$jam} jam {$menit} menit";
        }
        return "{$menit} menit";
    }
}
