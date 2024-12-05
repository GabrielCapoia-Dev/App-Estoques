<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;


    protected $fillable = [
        'nome_categoria',
        'status_categoria',
        'descricao_categoria',
    ];
    
    // Relação com os produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_categoria');
    }
}
