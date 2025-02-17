<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Painel; 
use App\Models\Modulo; 
use App\Models\Usuario; 

use Illuminate\Support\Facades\DB;


class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('api')->except('store', 'read', 'update');
    }
    
    public function store(Request $request, $id_painel)
    {
        $painel = Painel::where('id', $id_painel)->first(); 
    
        if (!$painel) {
            return response()->json(['message' => 'Painel não encontrado'], 404);
        }

        $contador = Modulo::where('id_painel', $id_painel)->count();

        if ($contador >= 5) {
            return response()->json(['message' => 'Limite de 5 módulos atingido para este painel.'], 400);
        }

    
        $request->validate([
            'nome' => 'nullable|string|max:200',
            'chave_http' => 'nullable|numeric|digits_between:1,20',
            'descricao' => 'nullable|string|max:500',
            'endereco' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'intervalo_atualizacao' => 'nullable|integer|min:1',
            'ip_autorizado' => 'nullable|ip'
        ]);
    
        $modulo = Modulo::create([
            'id_painel' => $id_painel,
            'nome' => $request->input('nome'),
            'chave_http' => $request->input('chave_http'),
            'descricao' => $request->input('descricao'),
            'endereco' => $request->input('endereco'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'intervalo_de_atualizacao' => $request->input('intervalo_atualizacao'),
            'ip_autorizado' => $request->input('ip_autorizado'),
        ]);
    
        return response()->json(['message' => 'Módulo criado com sucesso!', 'data' => $modulo], 201);
    }

    public function read($id_usuario, $id_painel)
    {
        $painel = Painel::where('id_usuario', $id_usuario)->first(); 
        $modulo = Modulo::where('id_painel', $id_painel)->first(); 
        
        if($painel && $modulo) {
            return response()->json($modulo, 200); 
        }
    }

    public function update(Request $request, $id_usuario, $id_painel)
    {
        $painel = Painel::where('id_usuario', $id_usuario)->first();
        $modulo = Modulo::where('id_painel', $id_painel)->first(); 
        
        $newmodulo = $request->validate([
            'nome' => 'nullable|string|max:200',
            'chave_http' => 'nullable|numeric|digits_between:1,20',
            'descricao' => 'nullable|string|max:500',
            'endereco' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'intervalo_atualizacao' => 'nullable|integer|min:1',
            'ip_autorizado' => 'nullable|ip'
        ]);

        if ($modulo) {
            DB::table('modulos')->where('id_painel', $id_painel)->update($newmodulo); 
            return response()->json(['message' => 'Módulo atualizado com sucesso!']);
        } else {
            return response()->json(['message' => 'Módulo não existe no sistema!']); 
        }
    }

    public function destroy(Request $request, $id_usuario, $id_painel, $id)
    {
        DB::beginTransaction();
    
        try {
            
            $painel = Painel::where('id', $id_painel)->where('id_usuario', $id_usuario)->first();
            if (!$painel) {
                return response()->json(['error' => 'Painel não encontrado ou não pertence ao usuário.'], 404);
            }
    
            $modulo = Modulo::where('id', $id)->where('id_painel', $id_painel)->first();
            if (!$modulo) {
                return response()->json(['error' => 'Módulo não encontrado ou não pertence ao painel.'], 404);
            }
    
            $modulo->delete();
    
            DB::commit();
            return response()->json(['message' => 'Módulo excluído com sucesso!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao excluir módulo: ' . $e->getMessage()], 500);
        }
    }

    private function removerAcentos($string)
    {
    return strtr(
        utf8_decode($string), 
        utf8_decode('ÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛÇáàâãéèêíìîóòôõúùûç'), 
        'AAAAEEEIIIOOOOUUUCaaaaeeeiiioooouuuc'
    );
    }

    public function search(Request $request, $id_usuario, $id_painel)
    {
        $campo = $request->input('campo'); 
        $valor = $request->input('valor'); 
    
        $camposPermitidos = ['nome', 'chave_http', 'descricao', 'ip_autorizado']; 
    
        if (!in_array($campo, $camposPermitidos)) {
            return response()->json(['error' => 'Campo de pesquisa não encontrado!'], 400); 
        }
    
        $painel = Painel::where('id', $id_painel)
                        ->where('id_usuario', $id_usuario)
                        ->first(); 
    
        if (!$painel) {
            return response()->json(['error' => 'Painel não encontrado!'], 404); 
        }
        
        $valorSemAcento = $this->removerAcentos($valor);
    
        $modulos = Modulo::where('id_painel', $id_painel)
            ->whereRaw("LOWER(unaccent($campo)) LIKE LOWER(unaccent(?))", ["%{$valorSemAcento}%"])
            ->get(); 
    
        if ($modulos->isEmpty()) {
            return response()->json(['message' => 'Nenhum módulo encontrado!'], 404); 
        }
    
        return response()->json($modulos, 200);
    }
    

    
}
