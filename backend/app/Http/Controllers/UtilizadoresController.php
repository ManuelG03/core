<?php

namespace App\Http\Controllers;

use App\Models\Utilizadores;
use Illuminate\Http\Request;

class UtilizadoresController extends Controller
{
    /**
     * Display a listing of the resource. Mostra todos.
     */
    public function index()
    {
        //$utilizadores = Utilizadores::with('posts')->get();
        //return response()->json($utilizadores);

        return Utilizadores::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource. ITEM ÚNICO.
     */
    public function show(Utilizadores $utilizadores)
    {
        $utilizadores = Utilizadores::with('posts')->find($utilizadores->id);
        return response()->json($utilizadores);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Utilizadores $utilizadores)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Utilizadores $utilizadores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utilizadores $utilizadores)
    {
        //
    }

    public function getUserPosts($id)
    {
        $utilizador = Utilizadores::with('posts')->find($id);

        if (!$utilizador) {
            return response()->json(['message' => 'Utilizador não encontrado'], 404);
        }

        return response()->json($utilizador->posts);
    }
}
