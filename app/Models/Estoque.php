<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_local',
        'nome_estoque',
        'status_estoque',
        'descricao_estoque',
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'id_local');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'estoque_produto')
            ->withPivot('id', 'quantidade_atual', 'quantidade_minima', 'quantidade_maxima', 'validade');
    }
}
