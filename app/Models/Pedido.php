<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'id_local',
        'estoque_produto_pedido_id',
    ];

    /**
     * Relacionamento com Local (um pedido pertence a um local).
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local');
    }

    /**
     * Relacionamento com EstoqueProdutoPedido (um pedido pertence a um item da tabela estoque_produto_pedido).
     */
    public function estoqueProdutoPedido()
    {
        return $this->belongsTo(EstoqueProdutoPedido::class, 'estoque_produto_pedido_id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class)->withPivot('quantidade');
    }

}
