<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'game_id',
        'round_number',
        'voter_id',
        'voted_id',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function voter()
    {
        return $this->belongsTo(Player::class, 'voter_id');
    }

    public function voted()
    {
        return $this->belongsTo(Player::class, 'voted_id');
    }
}
