<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_produto',
        'nome_produto',
        'status_produto',
        'descricao_produto',
        'preco',
        'quantidade_atual',
        'quantidade_minima',
        'quantidade_maxima',
        'validade',
    ];


    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}