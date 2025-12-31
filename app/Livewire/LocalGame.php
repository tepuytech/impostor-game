<?php

namespace App\Livewire;

use Livewire\Component;

class LocalGame extends Component
{
    public $players = [];
    public $newPlayerName = '';
    public $gameStarted = false;
    public $currentPlayerIndex = 0;
    public $phase = 'setup'; // setup, word, voting, result
    public $secretWord = '';
    public $impostorIndex = null;
    public $words = [];
    public $votes = [];
    public $eliminated = [];
    public $showWord = false;
    public $currentRound = 1;
    public $category = 'todas';
    public $timeLimit = 30;
    public $hasTimeLimit = false;
    public $timeRemaining = null;

    public function mount()
    {
        $this->players = [];
    }

    public function addPlayer()
    {
        $this->validate([
            'newPlayerName' => 'required|string|max:50'
        ]);

        // Validar que el nombre no esté repetido
        foreach ($this->players as $player) {
            if (strtolower($player['name']) === strtolower($this->newPlayerName)) {
                $this->addError('newPlayerName', 'Este nombre ya está en uso.');
                return;
            }
        }

        $this->players[] = [
            'name' => $this->newPlayerName,
            'isAlive' => true,
        ];

        $this->newPlayerName = '';
    }

    public function removePlayer($index)
    {
        unset($this->players[$index]);
        $this->players = array_values($this->players);
    }

    public function startGame()
    {
        if (count($this->players) < 3) {
            session()->flash('error', 'Se necesitan al menos 3 jugadores.');
            return;
        }

        $this->impostorIndex = array_rand($this->players);

        // Obtener palabras de la categoría seleccionada
        $allWords = config('words')[$this->category] ?? config('words')['todas'];
        $this->secretWord = $allWords[array_rand($allWords)];

        $this->gameStarted = true;
        $this->phase = 'word';
        $this->currentPlayerIndex = 0;
        $this->showWord = false;
        $this->timeRemaining = $this->hasTimeLimit ? $this->timeLimit : null;
    }

    public function showWordToPlayer()
    {
        $this->showWord = true;

        // Iniciar timer si está activado
        if ($this->hasTimeLimit) {
            $this->timeRemaining = $this->timeLimit;
        }
    }

    public function nextPlayer()
    {
        $this->showWord = false;
        $this->timeRemaining = null;
        $this->currentPlayerIndex++;

        // Saltar jugadores eliminados
        while (
            $this->currentPlayerIndex < count($this->players) &&
            !$this->players[$this->currentPlayerIndex]['isAlive']
        ) {
            $this->currentPlayerIndex++;
        }

        // Si todos vieron su palabra, ir a votación
        if ($this->currentPlayerIndex >= count($this->players)) {
            $this->phase = 'voting';
            $this->currentPlayerIndex = 0;

            // Saltar al primer jugador vivo para votar
            while (
                $this->currentPlayerIndex < count($this->players) &&
                !$this->players[$this->currentPlayerIndex]['isAlive']
            ) {
                $this->currentPlayerIndex++;
            }

            $this->votes = array_fill(0, count($this->players), null);
        }
    }

    public function decrementTimer()
    {
        if ($this->timeRemaining !== null && $this->timeRemaining > 0) {
            $this->timeRemaining--;
        }
    }

    public function timeExpired()
    {
        // Auto avanzar cuando se acaba el tiempo
        if ($this->showWord) {
            $this->nextPlayer();
        }
    }

    public function vote($votedIndex)
    {
        $this->votes[$this->currentPlayerIndex] = $votedIndex;
        $this->currentPlayerIndex++;

        // Saltar jugadores eliminados
        while (
            $this->currentPlayerIndex < count($this->players) &&
            !$this->players[$this->currentPlayerIndex]['isAlive']
        ) {
            $this->currentPlayerIndex++;
        }

        // Si todos votaron, procesar votos
        if ($this->currentPlayerIndex >= count($this->players)) {
            $this->processVotes();
        }
    }

    private function processVotes()
    {
        $voteCounts = array_count_values(array_filter($this->votes, fn($v) => $v !== null));
        arsort($voteCounts);
        $mostVotedIndex = array_key_first($voteCounts);

        // Verificar si votaron al impostor
        if ($mostVotedIndex === $this->impostorIndex) {
            $this->phase = 'result';
            return;
        }

        // Eliminar al jugador votado
        $this->players[$mostVotedIndex]['isAlive'] = false;
        $this->eliminated[] = $mostVotedIndex;

        // Contar jugadores vivos
        $aliveCount = count(array_filter($this->players, fn($p) => $p['isAlive']));

        // Si quedan solo 2 jugadores, gana el impostor
        if ($aliveCount <= 2) {
            $this->phase = 'result';
            return;
        }

        // Siguiente ronda
        $this->currentRound++;
        $this->phase = 'word';
        $this->currentPlayerIndex = 0;

        // Saltar al primer jugador vivo
        while (
            $this->currentPlayerIndex < count($this->players) &&
            !$this->players[$this->currentPlayerIndex]['isAlive']
        ) {
            $this->currentPlayerIndex++;
        }

        $this->showWord = false;
        $this->votes = array_fill(0, count($this->players), null);
    }

    public function restartGame()
    {
        $this->gameStarted = false;
        $this->phase = 'setup';
        $this->currentPlayerIndex = 0;
        $this->words = [];
        $this->votes = [];
        $this->eliminated = [];
        $this->showWord = false;
        $this->currentRound = 1;

        // Revivir a todos
        foreach ($this->players as &$player) {
            $player['isAlive'] = true;
        }
    }

    public function exitGame()
    {
        return $this->redirect(route('home'));
    }

    public function render()
    {
        return view('livewire.local-game');
    }
}