<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Painel extends Model
{
    use HasFactory; 

    protected $table = 'paineis'; 

    protected $fillable = [
        'id_usuario', 
        'nome'
    ]; 
    
    public $timestamps = true; 

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario'); 
    }
}
