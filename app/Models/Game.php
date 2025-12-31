<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'code',
        'status',
        'host_id',
        'current_round',
        'secret_word',
        'winner',
        'phase',
        'category',
        'time_limit',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function words()
    {
        return $this->hasMany(Word::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public static function generateCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}