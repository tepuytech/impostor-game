<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Player;
use App\Models\Vote;
use App\Models\Word;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GamePlay extends Component
{
    public $game;
    public $player;
    public $word_input = '';
    public $selected_vote;
    public $timeRemaining = null;

    protected $listeners = ['refreshGame' => '$refresh'];

    public function mount($code)
    {
        $this->game = Game::where('code', $code)->firstOrFail();
        $this->player = $this->game->players()->where('user_id', Auth::id())->firstOrFail();

        // Inicializar timer si está configurado
        if ($this->game->time_limit && $this->game->phase === 'word') {
            $this->timeRemaining = $this->game->time_limit;
        }
    }

    public function submitWord()
    {
        if (!$this->player->is_alive)
            return;

        $this->validate(['word_input' => 'required|string|max:50']);

        Word::create([
            'game_id' => $this->game->id,
            'player_id' => $this->player->id,
            'round_number' => $this->game->current_round,
            'word' => $this->word_input,
        ]);

        $this->word_input = '';
    }

    public function advanceToVoting()
    {
        // Solo el host puede avanzar
        if (!$this->player->is_host)
            return;

        if (!$this->allWordsSubmitted()) {
            session()->flash('error', 'No todos han enviado su palabra.');
            return;
        }

        $this->game->update(['phase' => 'voting']);
        $this->timeRemaining = null; // Resetear timer
    }

    public function submitVote()
    {
        if (!$this->player->is_alive)
            return;

        $this->validate(['selected_vote' => 'required|exists:players,id']);

        Vote::create([
            'game_id' => $this->game->id,
            'round_number' => $this->game->current_round,
            'voter_id' => $this->player->id,
            'voted_id' => $this->selected_vote,
        ]);

        $this->selected_vote = null;
    }

    public function processVotesManually()
    {
        // Solo el host puede procesar votos
        if (!$this->player->is_host)
            return;

        if (!$this->allVotesSubmitted()) {
            session()->flash('error', 'No todos han votado aún.');
            return;
        }

        $this->processVotes();
    }

    public function leaveGame()
    {
        // Marcar al jugador como eliminado
        $this->player->update(['is_alive' => false]);

        // Verificar si el juego debe terminar
        $alivePlayers = $this->game->players()->where('is_alive', true)->count();
        if ($alivePlayers <= 2) {
            $this->game->update([
                'status' => 'finished',
                'winner' => 'impostor',
            ]);
        }

        return $this->redirect(route('home'));
    }

    public function decrementTimer()
    {
        if ($this->timeRemaining !== null && $this->timeRemaining > 0) {
            $this->timeRemaining--;
        }
    }

    public function initTimer()
    {
        if ($this->game->time_limit) {
            $this->timeRemaining = $this->game->time_limit;
        }
    }

    private function allWordsSubmitted()
    {
        $alivePlayers = $this->game->players()->where('is_alive', true)->count();
        $wordsCount = Word::where('game_id', $this->game->id)
            ->where('round_number', $this->game->current_round)
            ->count();

        return $wordsCount >= $alivePlayers;
    }

    private function allVotesSubmitted()
    {
        $alivePlayers = $this->game->players()->where('is_alive', true)->count();
        $votesCount = Vote::where('game_id', $this->game->id)
            ->where('round_number', $this->game->current_round)
            ->count();

        return $votesCount >= $alivePlayers;
    }

    private function processVotes()
    {
        $votes = Vote::where('game_id', $this->game->id)
            ->where('round_number', $this->game->current_round)
            ->get();

        $voteCounts = $votes->groupBy('voted_id')->map->count()->sortDesc();
        $mostVoted = $voteCounts->keys()->first();
        $votedPlayer = Player::find($mostVoted);

        if ($votedPlayer->role === 'impostor') {
            $this->game->update([
                'status' => 'finished',
                'winner' => 'crewmates',
                'phase' => 'result',
            ]);
            return;
        }

        $votedPlayer->update(['is_alive' => false]);

        $alivePlayers = $this->game->players()->where('is_alive', true)->count();
        if ($alivePlayers <= 2) {
            $this->game->update([
                'status' => 'finished',
                'winner' => 'impostor',
                'phase' => 'result',
            ]);
            return;
        }

        $this->game->increment('current_round');
        $this->game->update(['phase' => 'word']);

        // Reiniciar timer si está configurado
        if ($this->game->time_limit) {
            $this->timeRemaining = $this->game->time_limit;
        }
    }

    public function render()
    {
        // Recargar datos actualizados en cada render
        $this->game->refresh();
        $this->player = $this->game->players()->where('user_id', Auth::id())->first();

        return view('livewire.game-play', [
            'game' => $this->game,
            'player' => $this->player,
            'alivePlayers' => $this->game->players()->where('is_alive', true)->with('user')->get(),
            'roundWords' => Word::where('game_id', $this->game->id)
                ->where('round_number', $this->game->current_round)
                ->with('player.user')
                ->get(),
        ]);
    }
}