<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Impostor Game' }}</title>
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">🎮 Impostor Game</h1>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Cerrar sesion</button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="container mx-auto p-4">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>