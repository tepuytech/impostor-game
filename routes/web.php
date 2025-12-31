<?php

use App\Livewire\Login;
use App\Livewire\Register;
use App\Livewire\Home;
use App\Livewire\GameLobby;
use App\Livewire\GamePlay;
use App\Livewire\LocalGame;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/local', LocalGame::class)->name('game.local');
    Route::get('/game/{code}/lobby', GameLobby::class)->name('game.lobby');
    Route::get('/game/{code}/play', GamePlay::class)->name('game.play');

    Route::post('/logout', function () {
        auth()->logout();
        return redirect()->route('login');
    })->name('logout');
});
