<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    protected $table = 'sensores'; 
    protected $fillable = [ 
        'id_modulo',
        's', 
        'codigo', 
        'nome', 
        'descricao', 
        'equacao', 
        'unidade', 
        'ref_grafico_1',
        'ref_grafico_2', 
        'titulo_ref_grafico_1', 
        'titulo_ref_grafico_2', 
        'modbus' 

    ]; 

    public $timestamps = true; 
}