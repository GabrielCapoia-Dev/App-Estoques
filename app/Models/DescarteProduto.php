<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescarteProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_estoque_produto',
        'defeito_descarte',
        'descricao_descarte',
        'quantidade_descarte',
    ];

    /**
     * Relacionamento com a tabela de estoque_produto.
     * 
     * Isso nos permite acessar o produto associado ao descarte.
     */
    public function estoqueProduto()
    {
        return $this->belongsTo(EstoqueProduto::class, 'id_estoque_produto');
    }
}