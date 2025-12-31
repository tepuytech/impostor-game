<div>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="bg-gray-800 p-8 rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-4xl font-bold">🎮 Modo Local</h2>
                @if($gameStarted)
                    <button 
                        wire:click="exitGame"
                        class="bg-red-600 px-4 py-2 rounded font-bold hover:bg-red-700"
                        onclick="return confirm('¿Seguro que quieres salir? Se perderá el progreso del juego.')"
                    >
                        ❌ Salir
                    </button>
                @endif
            </div>
            
            @if(!$gameStarted)
                <!-- Configuración inicial -->
                <div>
                    <h3 class="text-2xl font-bold mb-4">Agregar Jugadores</h3>
                    
                    <form wire:submit.prevent="addPlayer" class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input 
                                type="text" 
                                wire:model="newPlayerName" 
                                placeholder="Nombre del jugador..."
                                class="flex-1 p-3 rounded bg-gray-700 text-white"
                                required
                            >
                            <button type="submit" class="bg-blue-600 px-6 py-3 rounded font-bold hover:bg-blue-700 whitespace-nowrap">
                                ➕ Agregar
                            </button>
                        </div>
                        @error('newPlayerName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </form>

                    @if(session()->has('error'))
                        <div class="bg-red-600 text-white p-4 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Configuración -->
                    <div class="bg-gray-700 p-6 rounded-lg mb-6">
                        <h4 class="font-bold mb-4">⚙️ Configuración</h4>
                        
                        <!-- Categoría -->
                        <div class="mb-4">
                            <label class="block mb-2 font-bold">Categoría:</label>
                            <select wire:model="category" class="w-full p-3 rounded bg-gray-600 text-white">
                                <option value="todas">🌍 Todas</option>
                                <option value="lugares">🏖️ Lugares</option>
                                <option value="comida">🍕 Comida</option>
                                <option value="deportes">⚽ Deportes</option>
                                <option value="objetos">📱 Objetos</option>
                                <option value="animales">🦁 Animales</option>
                                <option value="profesiones">👨‍⚕️ Profesiones</option>
                                <option value="entretenimiento">🎬 Entretenimiento</option>
                            </select>
                        </div>

                        <!-- Tiempo Límite -->
                        <div>
                            <label class="flex items-center mb-3">
                                <input 
                                    type="checkbox" 
                                    wire:model="hasTimeLimit" 
                                    class="mr-2 w-5 h-5"
                                >
                                <span class="font-bold">⏱️ Tiempo Límite</span>
                            </label>
                            
                            @if($hasTimeLimit)
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="range" 
                                        wire:model="timeLimit" 
                                        min="10" 
                                        max="120" 
                                        step="5"
                                        class="flex-1"
                                    >
                                    <span class="font-bold text-xl w-20 text-center">{{ $timeLimit }}s</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Lista de jugadores -->
                    <div class="mb-6">
                        <h4 class="font-bold mb-3">Jugadores ({{ count($players) }})</h4>
                        @if(count($players) > 0)
                            <div class="grid gap-2">
                                @foreach($players as $index => $player)
                                    <div class="bg-gray-700 p-3 rounded flex justify-between items-center">
                                        <span class="font-bold">{{ $player['name'] }}</span>
                                        <button 
                                            wire:click="removePlayer({{ $index }})"
                                            class="bg-red-600 px-3 py-1 rounded text-sm hover:bg-red-700"
                                        >
                                            ❌
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-center py-4">No hay jugadores aún</p>
                        @endif
                    </div>

                    @if(count($players) >= 3)
                        <button 
                            wire:click="startGame"
                            class="w-full bg-green-600 p-4 rounded font-bold text-xl hover:bg-green-700 mb-3"
                        >
                            🎮 Iniciar Juego
                        </button>
                    @else
                        <p class="text-center text-yellow-500 mb-3">Se necesitan al menos 3 jugadores</p>
                    @endif
                    
                    <a 
                        href="{{ route('home') }}"
                        class="block w-full bg-gray-600 p-4 rounded font-bold text-xl text-center hover:bg-gray-700"
                    >
                        🏠 Volver al Inicio
                    </a>
                </div>

            @elseif($phase === 'word')
                <!-- Mostrar palabra a cada jugador -->
                <div class="text-center">
                    @if($currentPlayerIndex < count($players) && $players[$currentPlayerIndex]['isAlive'])
                        @if(!$showWord)
                            <div class="mb-8">
                                <p class="text-gray-400 mb-2">Ronda {{ $currentRound }}</p>
                                <h3 class="text-3xl font-bold mb-6">Turno de:</h3>
                                <p class="text-5xl font-bold text-blue-500 mb-8">
                                    {{ $players[$currentPlayerIndex]['name'] }}
                                </p>
                                <p class="text-xl text-gray-400 mb-6">
                                    Los demás jugadores, ¡no miren la pantalla!
                                </p>
                            </div>
                            
                            <button 
                                wire:click="showWordToPlayer"
                                class="bg-blue-600 px-8 py-4 rounded font-bold text-xl hover:bg-blue-700"
                            >
                                👁️ Ver mi Palabra
                            </button>
                        @else
                            <div class="mb-8">
                                <!-- Timer -->
                                @if($hasTimeLimit && $timeRemaining !== null)
                                    <div class="mb-6" wire:poll.1s="decrementTimer">
                                        <div class="text-center">
                                            <div class="inline-block bg-{{ $timeRemaining <= 5 ? 'red' : 'blue' }}-600 px-6 py-3 rounded-full">
                                                <span class="text-4xl font-bold">⏱️ {{ $timeRemaining }}s</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Barra de progreso -->
                                        <div class="w-full bg-gray-700 rounded-full h-3 mt-4">
                                            <div 
                                                class="bg-{{ $timeRemaining <= 5 ? 'red' : 'blue' }}-600 h-3 rounded-full transition-all duration-1000"
                                                style="width: {{ ($timeRemaining / $timeLimit) * 100 }}%"
                                            ></div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($currentPlayerIndex === $impostorIndex)
                                    <p class="text-5xl font-bold text-red-500 mb-4">🎭 Eres el IMPOSTOR</p>
                                    <p class="text-2xl text-gray-400">¡No sabes la palabra!</p>
                                    <p class="text-lg text-gray-500 mt-4">Intenta adivinar de qué hablan los demás</p>
                                @else
                                    <p class="text-2xl font-bold text-blue-500 mb-4">Tu palabra es:</p>
                                    <p class="text-6xl font-bold mb-4">{{ $secretWord }}</p>
                                    <p class="text-lg text-gray-400">Di una palabra relacionada sin ser obvio</p>
                                @endif
                            </div>

                            <button 
                                wire:click="nextPlayer"
                                class="bg-green-600 px-8 py-4 rounded font-bold text-xl hover:bg-green-700"
                            >
                                Siguiente Jugador →
                            </button>
                            
                            @if($hasTimeLimit && $timeRemaining !== null && $timeRemaining <= 0)
                                <script>
                                    setTimeout(() => {
                                        @this.call('timeExpired');
                                    }, 100);
                                </script>
                            @endif
                        @endif
                    @endif
                </div>

            @elseif($phase === 'voting')
                <!-- Votación -->
                <div class="text-center">
                    <h3 class="text-3xl font-bold mb-6">🗳️ Votación</h3>
                    
                    @if($currentPlayerIndex < count($players) && $players[$currentPlayerIndex]['isAlive'])
                        <p class="text-2xl mb-6">Turno de votar:</p>
                        <p class="text-4xl font-bold text-blue-500 mb-8">
                            {{ $players[$currentPlayerIndex]['name'] }}
                        </p>
                        
                        <p class="text-xl text-gray-400 mb-6">¿Quién crees que es el impostor?</p>
                        
                        <div class="grid gap-3 max-w-md mx-auto">
                            @foreach($players as $index => $player)
                                @if($index !== $currentPlayerIndex && $player['isAlive'])
                                    <button 
                                        wire:click="vote({{ $index }})"
                                        class="bg-gray-700 p-4 rounded font-bold hover:bg-gray-600"
                                    >
                                        {{ $player['name'] }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

            @elseif($phase === 'result')
                <!-- Resultado -->
                <div class="text-center">
                    @php
                        $impostorWon = count(array_filter($players, fn($p) => $p['isAlive'])) <= 2;
                    @endphp

                    <h2 class="text-5xl font-bold mb-6">
                        @if($impostorWon)
                            🎭 ¡El Impostor Ganó!
                        @else
                            🎉 ¡Los Tripulantes Ganaron!
                        @endif
                    </h2>

                    <p class="text-3xl mb-6">El impostor era:</p>
                    <p class="text-5xl font-bold text-red-500 mb-8">
                        {{ $players[$impostorIndex]['name'] }}
                    </p>

                    <p class="text-2xl mb-4">Palabra secreta:</p>
                    <p class="text-4xl font-bold text-blue-500 mb-8">{{ $secretWord }}</p>

                    <div class="flex gap-4 justify-center">
                        <button 
                            wire:click="restartGame"
                            class="bg-blue-600 px-8 py-4 rounded font-bold text-xl hover:bg-blue-700"
                        >
                            🔄 Jugar de Nuevo
                        </button>
                        <a 
                            href="{{ route('home') }}"
                            class="bg-gray-600 px-8 py-4 rounded font-bold text-xl hover:bg-gray-700"
                        >
                            🏠 Volver al Inicio
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>