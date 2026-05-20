<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('content', 255);
            $table->foreignId('utilizador_id')->constrained('utilizadores')->onDelete('cascade');
            $table->timestamps();
        });

        // Inserir dados de exemplo
        DB::table('posts')->insert([
            ['content' => 'Post 1', 'utilizador_id' => 1],
            ['content' => 'Post 2', 'utilizador_id' => 1],
            ['content' => 'Post 3', 'utilizador_id' => 1],
            ['content' => 'Post 4', 'utilizador_id' => 2],
            ['content' => 'Post 5', 'utilizador_id' => 2],
            ['content' => 'Post 6', 'utilizador_id' => 2],
            ['content' => 'Post 7', 'utilizador_id' => 3],
            ['content' => 'Post 8', 'utilizador_id' => 3],
            ['content' => 'Post 9', 'utilizador_id' => 3],
            ['content' => 'Post 10', 'utilizador_id' => 3],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
