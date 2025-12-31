<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->foreignId('host_id')->constrained('users');
            $table->integer('current_round')->default(0);
            $table->string('secret_word')->nullable();
            $table->enum('winner', ['impostor', 'crewmates'])->nullable();
            $table->string('category')->default('todas');
            $table->integer('time_limit')->nullable();
            $table->enum('phase', ['word', 'voting', 'result'])->default('word');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
