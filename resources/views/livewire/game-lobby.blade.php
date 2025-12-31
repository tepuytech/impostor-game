<div wire:poll.2s>
    <div class="max-w-4xl mx-auto mt-10">
        <div class="bg-gray-800 p-8 rounded-lg">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold">Sala de Espera</h2>
                </div>
                <button 
                    wire:click="leaveGame"
                    class="bg-red-600 px-4 py-2 rounded font-bold hover:bg-red-700"
                    onclick="return confirm('¿Seguro que quieres salir de la sala?')"
                >
                    🚪 Salir
                </button>
            </div>
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold mb-4">Sala de Espera</h2>
                <div class="bg-gray-700 p-4 rounded inline-block">
                    <p class="text-gray-400 mb-2">Código de Sala:</p>
                    <p class="text-5xl font-bold tracking-widest">{{ $game->code }}</p>
                </div>
            </div>

            @if(session()->has('error'))
                <div class="bg-red-600 text-white p-4 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if($game->host_id === auth()->id() && $game->status === 'waiting')
                <!-- Configuración del Juego (solo host) -->
                <div class="bg-gray-700 p-6 rounded-lg mb-6">
                    <h3 class="text-xl font-bold mb-4">⚙️ Configuración</h3>
                    
                    <!-- Categoría -->
                    <div class="mb-4">
                        <label class="block mb-2 font-bold">Categoría de Palabras:</label>
                        <select wire:model="category" class="w-full p-3 rounded bg-gray-600 text-white">
                            <option value="todas">🌍 Todas las Categorías</option>
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
                    @if(false)
                    <div class="mb-4">
                        <label class="flex items-center mb-3">
                            <input 
                                type="checkbox" 
                                wire:model="hasTimeLimit" 
                                class="mr-2 w-5 h-5"
                            >
                            <span class="font-bold">⏱️ Activar Tiempo Límite</span>
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
                                <span class="font-bold text-xl w-20 text-center">{{ $timeLimit ?? 30 }}s</span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">Tiempo para decir cada palabra</p>
                        @endif
                    </div>
                    @endif
                </div>
            @endif

            <div class="mb-8">
                <h3 class="text-2xl font-bold mb-4">Jugadores ({{ $players->count() }})</h3>
                <div class="grid gap-3">
                    @foreach($players as $player)
                        <div class="bg-gray-700 p-4 rounded flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                    {{ substr($player->user->name, 0, 1) }}
                                </div>
                                <span class="font-bold">{{ $player->user->name }}</span>
                            </div>
                            @if($player->is_host)
                                <span class="bg-yellow-600 px-3 py-1 rounded text-sm">HOST</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if($game->host_id === auth()->id())
                <button 
                    wire:click="startGame" 
                    class="w-full bg-green-600 p-4 rounded font-bold text-xl hover:bg-green-700"
                    @if($players->count() < 3) disabled class="opacity-50 cursor-not-allowed" @endif
                >
                    Iniciar Juego
                </button>
                @if($players->count() < 3)
                    <p class="text-center text-yellow-500 mt-3">Se necesitan al menos 3 jugadores</p>
                @endif
            @else
                <p class="text-center text-gray-400">Esperando que el host inicie el juego...</p>
            @endif
        </div>
    </div>
</div>