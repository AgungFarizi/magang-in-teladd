<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratBalasan extends Model
{
    protected $table = 'surat_balasan';

    protected $fillable = [
        'nomor_surat',
        'proposal_id',
        'jenis',
        'perihal',
        'isi_surat',
        'file_surat',
        'tanggal_surat',
        'dibuat_oleh',
        'dikirim_pada',
        'sudah_dibaca',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'dikirim_pada' => 'datetime',
            'dibaca_pada' => 'datetime',
            'sudah_dibaca' => 'boolean',
        ];
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }

    public static function generateNomor(string $jenis): string
    {
        $tahun = date('Y');
        $prefix = $jenis === 'penerimaan' ? 'TER' : 'TOL';
        $urutan = static::whereYear('created_at', $tahun)->where('jenis', $jenis)->count() + 1;
        return sprintf('SURT-%s-%s-%04d', $prefix, $tahun, $urutan);
    }
}
