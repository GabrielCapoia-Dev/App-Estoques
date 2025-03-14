<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;


    protected $fillable = [
        'logradouro',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'complemento',
        'numero'
    ];
    
    
}