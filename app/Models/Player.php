<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'game_id',
        'user_id',
        'role',
        'is_alive',
        'is_host',
    ];

    protected $casts = [
        'is_alive' => 'boolean',
        'is_host' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function words()
    {
        return $this->hasMany(Word::class);
    }
}
