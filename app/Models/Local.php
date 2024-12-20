<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_endereco',
        'nome_local',
        'status_local',
    ];

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_locals');
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_local');
    }
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_local');
    }
}
