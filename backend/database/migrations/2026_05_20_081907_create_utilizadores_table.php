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
        Schema::create('utilizadores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->timestamps();
        });

        DB::table('utilizadores')->insert([
            ['name' => 'Manuel', 'username' => 'manuelg'],
            ['name' => 'Alice', 'username' => 'alice123'],
            ['name' => 'Bob', 'username' => 'bob456'],
            ['name' => 'Charlie', 'username' => 'charlie789'],
            ['name' => 'David', 'username' => 'david012'],
            ['name' => 'Eve', 'username' => 'eve345'],
            ['name' => 'Frank', 'username' => 'frank678'],
            ['name' => 'Grace', 'username' => 'grace901'],
            ['name' => 'Heidi', 'username' => 'heidi234'],
            ['name' => 'Ivan', 'username' => 'ivan567'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilizadores');
    }
};
