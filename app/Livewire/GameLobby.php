<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;

class GameLobby extends Component
{
    public $game;
    public $category = 'todas';
    public $timeLimit = null;
    public $hasTimeLimit = false;

    protected $listeners = ['refreshLobby' => '$refresh'];

    public function mount($code)
    {
        $this->game = Game::where('code', $code)->firstOrFail();

        // Si el juego ya comenzó, redirigir
        if ($this->game->status === 'playing') {
            $this->redirectRoute('game.play', ['code' => $this->game->code]);
        }
    }

    public function startGame()
    {
        $players = $this->game->players;

        if ($players->count() < 3) {
            session()->flash('error', 'Se necesitan al menos 3 jugadores.');
            return;
        }

        $impostor = $players->random();
        $impostor->update(['role' => 'impostor']);

        // Obtener palabras de la categoría seleccionada
        $words = config('words')[$this->category] ?? config('words')['todas'];
        $secretWord = $words[array_rand($words)];

        $this->game->update([
            'secret_word' => $secretWord,
            'status' => 'playing',
            'current_round' => 1,
            'category' => $this->category,
            'time_limit' => $this->hasTimeLimit ? $this->timeLimit : null,
        ]);

        $players->where('id', '!=', $impostor->id)->each(function ($player) {
            $player->update(['role' => 'crewmate']);
        });

        return redirect()->route('game.play', $this->game->code);
    }

    public function leaveGame()
    {
        // Eliminar al jugador de la partida
        $this->game->players()->where('user_id', auth()->id())->delete();

        // Si era el host y hay otros jugadores, asignar nuevo host
        if ($this->game->host_id === auth()->id()) {
            $newHost = $this->game->players()->first();
            if ($newHost) {
                $this->game->update(['host_id' => $newHost->user_id]);
                $newHost->update(['is_host' => true]);
            } else {
                // Si no hay más jugadores, eliminar la partida
                $this->game->delete();
            }
        }

        return $this->redirect(route('home'));
    }

    public function render()
    {
        // Recargar el juego para obtener el estado actualizado
        $this->game->refresh();

        // Si el juego ya comenzó, redirigir
        if ($this->game->status === 'playing') {
            $this->redirectRoute('game.play', ['code' => $this->game->code]);
        }

        return view('livewire.game-lobby', [
            'players' => $this->game->players()->with('user')->get(),
        ]);
    }
}