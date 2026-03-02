<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'pengguna_id',
        'judul',
        'pesan',
        'tipe',
        'url',
        'sudah_dibaca',
        'dibaca_pada',
        'notifiable_id',
        'notifiable_type',
    ];

    protected function casts(): array
    {
        return [
            'sudah_dibaca' => 'boolean',
            'dibaca_pada' => 'datetime',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('sudah_dibaca', false);
    }

    public static function kirim(int $penggunaId, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): self
    {
        return static::create([
            'pengguna_id' => $penggunaId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
        ]);
    }
}
