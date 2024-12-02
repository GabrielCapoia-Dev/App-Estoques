<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_endereco',
        'id_estoque',
        'nome_local',
        'status_local',
    ];

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_locals');
    }
}
