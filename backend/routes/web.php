<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AddUser;
use App\Http\Controllers\UtilizadoresController;
use App\Http\Controllers\PDFController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/add-user', [AddUser::class, 'add']);

// Rotas para utilizadores
Route::get(('/utilizadores'), [App\Http\Controllers\UtilizadoresController::class, 'index']);

// Rota para ir buscar posts de um utilizador específico
Route::get('/posts/{id}', [App\Http\Controllers\UtilizadoresController::class, 'getUserPosts']);

// Rota para dar upload de PDF para o backend
Route::post('/enviarpdf', [App\Http\Controllers\PDFController::class, 'upload2']);

Route::post('/exportar', [App\Http\Controllers\PDFController::class, 'export']);

