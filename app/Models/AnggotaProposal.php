<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaProposal extends Model
{
    protected $table = 'anggota_proposal';

    protected $fillable = [
        'proposal_id',
        'pengguna_id',
        'nama_lengkap',
        'nim',
        'email',
        'no_telepon',
        'jurusan',
        'institusi',
        'peran',
        'is_confirmed',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_confirmed' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
