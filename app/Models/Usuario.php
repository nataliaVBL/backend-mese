<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome', 
        'email', 
        'senha'
    ]; 

    public $timestamps = true; 

    public function painel()
    {
        return $this->hasMany(Painel::class, 'id_usuario'); 
    }
}
