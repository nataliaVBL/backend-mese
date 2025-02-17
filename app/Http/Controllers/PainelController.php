<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Painel; 
use App\Models\Usuario; 
use App\Models\Modulo;
use App\Models\Sensor;

use Illuminate\Support\Facades\DB;


class PainelController extends Controller
{
    public function __construct()
    {
        $this->middleware('api')->except('store', 'update');
    }
    
    public function store(Request $request, $id_usuario)
    {
        $usuario = Usuario::where('id', $id_usuario)->first(); 
    
        if (!$usuario) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
    
        $request->validate([
            'nome' => 'nullable|string|max:60', 
        ]);
    
        $painel = Painel::create([
            'id_usuario' => $id_usuario,
            'nome' => $request->input('nome'),
        ]);
    
        return response()->json(['message' => 'Painel criado com sucesso!', 'data' => $painel], 201);
    }
    
    
    public function update(Request $request, $id_usuario, $id_painel)
    {   

    $painel = Painel::where('id', $id_painel)
                    ->where('id_usuario', $id_usuario)
                    ->first();

    if (!$painel) {
        return response()->json(['message' => 'Painel não encontrado'], 404);
    }

    $request->validate([
        'nome' => 'nullable|string|max:60',
    ]);

    $painel->update([
        'nome' => $request->nome,
    ]);

    return response()->json([
        'message' => 'Nome do painel atualizado com sucesso!', 'data' => $painel], 200);
    } 
    
    public function destroy(Request $request, $id_usuario, $id_painel)
    {
        DB::beginTransaction(); 
    
        try {
            
            $painel = Painel::where('id', $id_painel)->where('id_usuario', $id_usuario)->first();
            if (!$painel) {
                return response()->json(['error' => 'Painel não encontrado ou não pertence ao usuário.'], 404);
            }
    
            $modulos = Modulo::where('id_painel', $id_painel)->get();
    
            foreach ($modulos as $modulo) {
                Sensor::where('id_modulo', $modulo->id)->delete();
                $modulo->delete();
            }
    
            $painel->delete();
    
            DB::commit(); 
            return response()->json(['message' => 'Painel e todos os seus módulos e sensores foram excluídos com sucesso!'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json(['error' => 'Erro ao excluir painel: ' . $e->getMessage()], 500);
        }
    } 

    public function read($id_usuario)
    {

    $paineis = Painel::where('id_usuario', $id_usuario)->get();

    if ($paineis->isEmpty()) {
        return response()->json(['message' => 'Nenhum painel encontrado para este usuário.'], 404);
    }

    return response()->json($paineis, 200); 

    }   

    private function removerAcentos($string)
    {
    return strtr(
        utf8_decode($string), 
        utf8_decode('ÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛÇáàâãéèêíìîóòôõúùûç'), 
        'AAAAEEEIIIOOOOUUUCaaaaeeeiiioooouuuc'
    );
    }

    public function search(Request $request, $id_usuario)
    {
        $campo = $request->input('campo'); 
        $valor = $request->input('valor'); 
    
        $camposPermitidos = ['nome']; 
    
        if (!in_array($campo, $camposPermitidos)) {
            return response()->json(['error' => 'Campo de pesquisa não permitido!'], 400); 
        }
    
        $usuario = Usuario::where('id', $id_usuario)->first();
        if (!$usuario) {
            return response()->json(['error' => 'Usuário não encontrado!'], 404);
        }
    
        $valorSemAcento = $this->removerAcentos($valor);
    
        $paineis = Painel::where('id_usuario', $id_usuario)
            ->whereRaw("LOWER(unaccent($campo)) LIKE LOWER(unaccent(?))", ["%{$valorSemAcento}%"])
            ->get(); 
    
        if ($paineis->isEmpty()) {
            return response()->json(['message' => 'Nenhum painel encontrado!'], 404); 
        }
    
        return response()->json($paineis, 200);
    }
}
