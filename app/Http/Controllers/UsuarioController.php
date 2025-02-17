<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; 

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('api')->except('store');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'nullable|string|max:60', 
            'email' => 'required|string|max:100', 
            'senha' => 'required|string|max:500'
        ]); 

        $usuario = Usuario::create([
            'nome' => $request->input('nome'),
            'email' => $request->input('email'), 
            'senha' => $request->input('senha')
        ]);

        return response()->json(['message' => 'Usuário criado com sucesso!', 'data' => $usuario], 201);
    }  
}
