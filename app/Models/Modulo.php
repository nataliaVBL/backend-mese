<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_painel', 
        'nome', 
        'chave_http', 
        'descricao', 
        'endereco', 
        'latitude', 
        'longitude', 
        'intervalo_de_atualizacao', 
        'ip_autorizado'
    ]; 

    public $timestamps = true; 

    public function painel()
    {
        return $this->belongsTo(Painel::class, 'id_painel'); 
    }
}
