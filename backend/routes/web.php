<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AddUser;
use App\Http\Controllers\UtilizadoresController;
use App\Http\Controllers\PDFController;

Route::get('/', function () {
    return view('welcome');
});

// Route para adicionar utilizadores (sem uso)
Route::post('/add-user', [AddUser::class, 'add']);

// Route para obter lista de utilizadores
Route::get(('/utilizadores'), [App\Http\Controllers\UtilizadoresController::class, 'index']);

// Route para obter os posts de cada um dos utilizadores
Route::get('/posts/{id}', [App\Http\Controllers\UtilizadoresController::class, 'getUserPosts']);

//Route para enviar o PDF para o backend, e obter o texto extraído
Route::post('/enviarpdf', [App\Http\Controllers\PDFController::class, 'upload']);

//Route para exportar o texto extraído para um ficheiro Excel
Route::post('/exportar', [App\Http\Controllers\PDFController::class, 'export']);

