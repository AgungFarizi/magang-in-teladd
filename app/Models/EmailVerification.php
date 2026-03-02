<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';

    protected $fillable = [
        'email',
        'token',
        'expired_at',
        'is_used',
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expired_at->isFuture();
    }

    public static function buat(string $email): self
    {
        // Hapus token lama
        static::where('email', $email)->delete();

        return static::create([
            'email' => $email,
            'token' => Str::random(64),
            'expired_at' => now()->addHours(24),
        ]);
    }
}
