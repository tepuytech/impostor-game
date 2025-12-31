<div>
    <div>
        <div class="max-w-md mx-auto mt-10 bg-gray-800 p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold mb-6 text-center">Iniciar Sesión</h2>

            <form wire:submit.prevent="login">
                <div class="mb-4">
                    <label class="block mb-2">Email</label>
                    <input type="email" wire:model="email" class="w-full p-2 rounded bg-gray-700 text-white" required>
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Contraseña</label>
                    <input type="password" wire:model="password" class="w-full p-2 rounded bg-gray-700 text-white"
                        required>
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="remember" class="mr-2">
                        <span>Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 p-3 rounded font-bold hover:bg-blue-700">
                    Entrar
                </button>
            </form>

            <p class="mt-4 text-center">
                ¿No tienes cuenta? <a href="{{ route('register') }}"
                    class="text-blue-400 hover:underline">Regístrate</a>
            </p>
        </div>
    </div>
</div>