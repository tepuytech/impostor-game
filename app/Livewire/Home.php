<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Home extends Component
{
    public $join_code = '';

    public function createGame()
    {
        $game = Game::create([
            'code' => Game::generateCode(),
            'host_id' => Auth::id(),
            'status' => 'waiting',
        ]);

        Player::create([
            'game_id' => $game->id,
            'user_id' => Auth::id(),
            'is_host' => true,
        ]);

        return redirect()->route('game.lobby', $game->code);
    }

    public function joinGame()
    {
        $this->validate([
            'join_code' => 'required|size:6|exists:games,code',
        ]);

        $game = Game::where('code', strtoupper($this->join_code))->first();

        if ($game->status !== 'waiting') {
            $this->addError('join_code', 'Esta partida ya comenzó o finalizó.');
            return;
        }

        if ($game->players()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('game.lobby', $game->code);
        }

        Player::create([
            'game_id' => $game->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('game.lobby', $game->code);
    }

    public function render()
    {
        return view('livewire.home')->layout('components.layouts.app');
    }
}