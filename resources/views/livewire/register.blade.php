<div>
    <div class="max-w-md mx-auto mt-10 bg-gray-800 p-8 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-6 text-center">Crear Cuenta</h2>

        <form wire:submit.prevent="register">
            <div class="mb-4">
                <label class="block mb-2">Nombre</label>
                <input type="text" wire:model="name" class="w-full p-2 rounded bg-gray-700 text-white" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input type="email" wire:model="email" class="w-full p-2 rounded bg-gray-700 text-white" required>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2">Contraseña</label>
                <input type="password" wire:model="password" class="w-full p-2 rounded bg-gray-700 text-white" required>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2">Confirmar Contraseña</label>
                <input type="password" wire:model="password_confirmation"
                    class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2">Género</label>
                <select wire:model="gender" class="w-full p-2 rounded bg-gray-700 text-white" required>
                    <option value="">Seleccionar</option>
                    <option value="male">Masculino</option>
                    <option value="female">Femenino</option>
                    <option value="other">Otro</option>
                    <option value="prefer_not_to_say">Prefiero no decir</option>
                </select>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block mb-2">Fecha de Nacimiento</label>
                <input type="date" wire:model="birth_date" class="w-full p-2 rounded bg-gray-700 text-white" required>
                @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 p-3 rounded font-bold hover:bg-blue-700">
                Registrarse
            </button>
        </form>

        <p class="mt-4 text-center">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Inicia sesión</a>
        </p>
    </div>
</div>