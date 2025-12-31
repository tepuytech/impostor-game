<div>
    <div class="max-w-4xl mx-auto mt-10">
        <h2 class="text-4xl font-bold mb-8 text-center">Bienvenido, {{ auth()->user()->name }}!</h2>

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Modo Local -->
            <div class="bg-gray-800 p-8 rounded-lg">
                <h3 class="text-2xl font-bold mb-4">🎮 Modo Local</h3>
                <p class="mb-6 text-gray-400">Juega con amigos en un solo dispositivo pasándose el teléfono.</p>
                <a href="{{ route('game.local') }}" class="block w-full bg-purple-600 p-3 rounded font-bold text-center hover:bg-purple-700">
                    Jugar Local
                </a>
            </div>

            <!-- Crear Sala -->
            <div class="bg-gray-800 p-8 rounded-lg">
                <h3 class="text-2xl font-bold mb-4">🌐 Crear Sala Online</h3>
                <p class="mb-6 text-gray-400">Crea una partida online y comparte el código.</p>
                <button wire:click="createGame" class="w-full bg-green-600 p-3 rounded font-bold hover:bg-green-700">
                    Crear Partida
                </button>
            </div>

            <!-- Unirse a Sala -->
            <div class="bg-gray-800 p-8 rounded-lg">
                <h3 class="text-2xl font-bold mb-4">🚪 Unirse a Sala</h3>
                <p class="mb-4 text-gray-400">Ingresa el código de la partida.</p>
                <form wire:submit.prevent="joinGame">
                    <input 
                        type="text" 
                        wire:model="join_code" 
                        placeholder="Código"
                        maxlength="6"
                        class="w-full p-3 mb-3 rounded bg-gray-700 text-white uppercase text-center text-xl font-bold"
                        required
                    >
                    @error('join_code') 
                        <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="w-full bg-blue-600 p-3 rounded font-bold hover:bg-blue-700">
                        Unirse
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>