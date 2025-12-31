<div wire:poll.2s>
    <div class="max-w-6xl mx-auto mt-10">
        @if($game->status === 'finished')
            <!-- Resultado Final -->
            <div class="bg-gray-800 p-8 rounded-lg text-center">
                <h2 class="text-5xl font-bold mb-6">
                    @if($game->winner === 'impostor')
                        🎭 ¡El Impostor Ganó!
                    @else
                        🎉 ¡Los Tripulantes Ganaron!
                    @endif
                </h2>
                <p class="text-2xl mb-8">
                    El impostor era: <span class="font-bold text-red-500">
                        {{ $game->players()->where('role', 'impostor')->first()->user->name }}
                    </span>
                </p>
                <p class="text-xl mb-8">
                    Palabra secreta: <span class="font-bold text-blue-500">{{ $game->secret_word }}</span>
                </p>
                <a href="{{ route('home') }}" class="bg-blue-600 px-8 py-4 rounded font-bold text-xl hover:bg-blue-700">
                    Volver al Inicio
                </a>
            </div>
        @else
            <!-- Información del jugador -->
            <div class="bg-gray-800 p-6 rounded-lg mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-400">Ronda: <span class="text-white font-bold">{{ $game->current_round }}</span></p>
                        <p class="text-gray-400">Jugadores vivos: <span class="text-white font-bold">{{ $alivePlayers->count() }}</span></p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            @if($player->is_alive)
                                @if($player->role === 'impostor')
                                    <p class="text-3xl font-bold text-red-500">🎭 Eres el IMPOSTOR</p>
                                    <p class="text-gray-400">¡No sabes la palabra!</p>
                                @else
                                    <p class="text-2xl font-bold text-blue-500">Tu palabra:</p>
                                    <p class="text-4xl font-bold">{{ $game->secret_word }}</p>
                                @endif
                            @else
                                <p class="text-2xl font-bold text-gray-500">❌ Eliminado</p>
                            @endif
                        </div>
                        <button 
                            wire:click="leaveGame"
                            class="bg-red-600 px-4 py-2 rounded font-bold hover:bg-red-700"
                            onclick="return confirm('¿Seguro que quieres salir? Perderás la partida.')"
                        >
                            🚪 Salir
                        </button>
                    </div>
                </div>
            </div>

            @if($game->phase === 'word')
                <!-- Fase de Palabras -->
                <div class="bg-gray-800 p-8 rounded-lg mb-6">
                    <h3 class="text-2xl font-bold mb-4">Di una palabra relacionada</h3>
                    
                    @if($player->is_alive)
                        @php
                            $hasSubmitted = $roundWords->where('player_id', $player->id)->isNotEmpty();
                        @endphp

                        @if(!$hasSubmitted)
                            <form wire:submit.prevent="submitWord">
                                <input 
                                    type="text" 
                                    wire:model="word_input" 
                                    placeholder="Escribe tu palabra..."
                                    class="w-full p-3 rounded bg-gray-700 text-white text-xl mb-3"
                                    required
                                >
                                <button type="submit" class="w-full bg-blue-600 p-3 rounded font-bold hover:bg-blue-700">
                                    Enviar Palabra
                                </button>
                            </form>
                        @else
                            <p class="text-green-500 text-center text-xl">✓ Palabra enviada. Esperando a otros jugadores...</p>
                        @endif
                    @endif

                    <!-- Palabras dichas esta ronda -->
                    <div class="mt-6">
                        <h4 class="font-bold mb-3">Palabras de esta ronda:</h4>
                        <div class="grid gap-2">
                            @foreach($roundWords as $word)
                                <div class="bg-gray-700 p-3 rounded flex justify-between items-center">
                                    <span>{{ $word->player->user->name }}:</span>
                                    <span class="font-bold text-xl">{{ $word->word }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @php
                        $wordsCount = $roundWords->count();
                        $aliveCount = $alivePlayers->count();
                    @endphp

                    @if($player->is_host && $wordsCount >= $aliveCount)
                        <button 
                            wire:click="advanceToVoting" 
                            class="w-full mt-6 bg-blue-600 p-4 rounded font-bold text-xl hover:bg-blue-700"
                        >
                            ➡️ Iniciar Votación
                        </button>
                    @endif
                </div>

            @elseif($game->phase === 'voting')
                <!-- Fase de Votación -->
                <div class="bg-gray-800 p-8 rounded-lg">
                    <h3 class="text-2xl font-bold mb-4">¿Quién es el impostor?</h3>
                    
                    @if($player->is_alive)
                        @php
                            $hasVoted = \App\Models\Vote::where('game_id', $game->id)
                                ->where('round_number', $game->current_round)
                                ->where('voter_id', $player->id)
                                ->exists();
                            $totalVotes = \App\Models\Vote::where('game_id', $game->id)
                                ->where('round_number', $game->current_round)
                                ->count();
                            $alivePlayersCount = $game->players()->where('is_alive', true)->count();
                        @endphp

                        @if(!$hasVoted)
                            <form wire:submit.prevent="submitVote">
                                <div class="grid gap-3 mb-6">
                                    @foreach($alivePlayers as $p)
                                        @if($p->id !== $player->id)
                                            <label class="bg-gray-700 p-4 rounded cursor-pointer hover:bg-gray-600 flex items-center">
                                                <input 
                                                    type="radio" 
                                                    wire:model="selected_vote" 
                                                    value="{{ $p->id }}" 
                                                    class="mr-3"
                                                >
                                                <span class="font-bold">{{ $p->user->name }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                                <button type="submit" class="w-full bg-red-600 p-3 rounded font-bold hover:bg-red-700">
                                    Votar
                                </button>
                            </form>
                        @else
                            <p class="text-green-500 text-center text-xl mb-6">✓ Voto registrado. Esperando a otros jugadores...</p>
                            <p class="text-center text-gray-400">Votos: {{ $totalVotes }}/{{ $alivePlayersCount }}</p>
                        @endif

                        @if($player->is_host && $totalVotes >= $alivePlayersCount)
                            <button 
                                wire:click="processVotesManually" 
                                class="w-full mt-6 bg-green-600 p-4 rounded font-bold text-xl hover:bg-green-700"
                            >
                                📊 Contabilizar Votos
                            </button>
                        @endif
                    @else
                        <p class="text-center text-gray-400">Has sido eliminado.</p>
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>