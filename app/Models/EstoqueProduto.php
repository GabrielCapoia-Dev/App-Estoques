<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstoqueProduto extends Model
{
    protected $table = 'estoque_produto';  // Nome da tabela

    // Relacionamento com o modelo Estoque
    public function estoque()
    {
        return $this->belongsTo(Estoque::class, 'estoque_id');
    }

    // Relacionamento com o modelo Produto
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function descartes()
    {
        return $this->hasMany(DescarteProdutos::class, 'id_estoque_produto');
    }
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'estoque_produto_pedido')
            ->withPivot('quantidade', 'preco_unitario') // Colunas adicionais
            ->withTimestamps();
    }
    public function estoqueProdutoPedidos()
    {
        return $this->hasMany(EstoqueProdutoPedido::class, 'estoque_produto_id');
    }
}
