<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor; 
use App\Models\Modulo; 
use App\Models\Painel; 

use Illuminate\Support\Facades\DB;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('api')->except('store');
    }
    
    public function store(Request $request, $id_modulo)
    {
        $modulo = Modulo::where('id', $id_modulo)->first(); 

        if (!$modulo) {
            return response()->json(['message' => 'Módulo não encontrado'], 404);
        }

        $request->validate([
            's' => 'nullable|integer',
            'codigo' => 'nullable|integer',
            'nome' => 'nullable|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'equacao' => 'nullable|string|max:200',
            'unidade' => 'nullable|string|max:20',
            'ref_grafico_1' => 'nullable|numeric',
            'ref_grafico_2' => 'nullable|numeric',
            'titulo_ref_grafico_1' => 'nullable|string|max:100',
            'titulo_ref_grafico_2' => 'nullable|string|max:100',
            'modbus' => 'nullable|integer'
        ]);
        
        $sensor = Sensor::create([
            'id_modulo' => $id_modulo, 
            's' => $request->input('s'),
            'codigo' => $request->input('codigo'),
            'nome' => $request->input('nome'),
            'descricao' => $request->input('descricao'),
            'equacao' => $request->input('equacao'),
            'unidade' => $request->input('unidade'),
            'ref_grafico_1' => $request->input('ref_grafico_1'),
            'ref_grafico_2' => $request->input('ref_grafico_2'),
            'titulo_ref_grafico_1' => $request->input('titulo_ref_grafico_1'),
            'titulo_ref_grafico_2' => $request->input('titulo_ref_grafico_2'),
            'modbus' => $request->input('modbus'),
        ]);
        
        return response()->json(['message' => 'Sensor criado com sucesso!', 'data' => $sensor], 201);
    }

    public function update(Request $request, $id_usuario, $id_painel, $id_modulo)
    {
        $painel = Painel::where('id', $id_painel)->where('id_usuario', $id_usuario)->first();
        
        if (!$painel) {
            return response()->json(['message' => 'Painel não encontrado para este usuário!'], 404);
        }

        $modulo = Modulo::where('id', $id_modulo)->where('id_painel', $id_painel)->first();
        
        if (!$modulo) {
            return response()->json(['message' => 'Módulo não encontrado para este painel!'], 404);
        }

        $sensor = Sensor::where('id_modulo', $id_modulo)->first();

        $newsensor = $request->validate([
            's' => 'nullable|integer',
            'codigo' => 'nullable|integer',
            'nome' => 'nullable|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'equacao' => 'nullable|string|max:200',
            'unidade' => 'nullable|string|max:20',
            'ref_grafico_1' => 'nullable|numeric',
            'ref_grafico_2' => 'nullable|numeric',
            'titulo_ref_grafico_1' => 'nullable|string|max:100',
            'titulo_ref_grafico_2' => 'nullable|string|max:100',
            'modbus' => 'nullable|integer',
        ]);

        if (!$sensor) {
            return response()->json(['message' => 'Sensor não encontrado neste módulo!'], 404);
        } else {
            DB::table('sensores')->where('id_modulo', $id_modulo)->update($newsensor); 
            return response()->json(['message' => 'Sensor atualizado com sucesso!']);
        }
    }
    public function destroy(Request $request, $id_usuario, $id_painel, $id_modulo, $id_sensor)
    {
        DB::beginTransaction();
    
        try {
            
            $painel = Painel::where('id', $id_painel)->where('id_usuario', $id_usuario)->first();
            if (!$painel) {
                return response()->json(['error' => 'Painel não encontrado ou não pertence ao usuário.'], 404);
            }
            
            $modulo = Modulo::where('id', $id_modulo)->where('id_painel', $id_painel)->first();
            if (!$modulo) {
                return response()->json(['error' => 'Módulo não encontrado ou não pertence ao painel.'], 404);
            }
    
            $sensor = Sensor::where('id', $id_sensor)->where('id_modulo', $id_modulo)->first();
            if (!$sensor) {
                return response()->json(['error' => 'Sensor não encontrado ou não pertence ao módulo!'], 404);
            }
    
            $sensor->delete();
    
            DB::commit();
            return response()->json(['message' => 'Sensor excluído com sucesso!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao excluir sensor: ' . $e->getMessage()], 500);
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

    public function search(Request $request, $id_usuario, $id_painel, $id_modulo)
    {
        $campo = $request->input('campo'); 
        $valor = $request->input('valor'); 
    
        $camposPermitidos = ['codigo', 'nome', 'descricao']; 
    
        if (!in_array($campo, $camposPermitidos)) {
            return response()->json(['error' => 'Campo de pesquisa não encontrado!'], 400); 
        }
    
        $painel = Painel::where('id', $id_painel)
                        ->where('id_usuario', $id_usuario)
                        ->first(); 
    
        if (!$painel) {
            return response()->json(['error' => 'Painel não encontrado!'], 404); 
        }

        $modulo = Modulo::where('id', $id_modulo)
                        ->where('id_painel', $id_painel)
                        ->first(); 
    
        if (!$modulo) {
            return response()->json(['error' => 'Módulo não encontrado!'], 404); 
        }
        
        $valorSemAcento = $this->removerAcentos($valor);
    
        $sensores = Sensor::where('id_modulo', $id_modulo)
            ->whereRaw("LOWER(unaccent($campo)) LIKE LOWER(unaccent(?))", ["%{$valorSemAcento}%"])
            ->get(); 
    
        if ($sensores->isEmpty()) {
            return response()->json(['message' => 'Nenhum módulo encontrado!'], 404); 
        }
    
        return response()->json($sensores, 200);
    }

    public function read($id_usuario, $id_painel, $id_modulo)
    {
        $painel = Painel::where('id_usuario', $id_usuario)->first(); 
        $modulo = Modulo::where('id_painel', $id_painel)->first(); 
        $sensor = Sensor::where('id_modulo', $id_modulo)->first(); 
        
        if($painel && $modulo && $sensor) {
            return response()->json($sensor, 200); 
        }
    }
    
}
