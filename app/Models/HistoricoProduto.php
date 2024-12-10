<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_produto',
        'id_categoria',
        'id_estoque',
        'id_local',
        'nome_categoria',
        'nome_estoque',
        'nome_local',
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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function estoque()
    {
        return $this->belongsTo(Estoque::class, 'id_estoque');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local');
    }
}