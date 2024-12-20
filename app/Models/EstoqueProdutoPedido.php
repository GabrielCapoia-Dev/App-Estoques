<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoqueProdutoPedido extends Model
{
    use HasFactory;

    protected $table = 'estoque_produto_pedido';

    protected $fillable = [
        'estoque_produto_id',
        'pedido_id',
        'quantidade',
        'preco_unitario',
    ];

    /**
     * Relacionamento com EstoqueProduto (um registro pertence a um produto em estoque).
     */
    public function estoqueProduto()
    {
        return $this->belongsTo(EstoqueProduto::class, 'estoque_produto_id');
    }

    /**
     * Relacionamento com Pedido (um registro pode ter múltiplos pedidos associados).
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'estoque_produto_pedido_id');
    }
}
