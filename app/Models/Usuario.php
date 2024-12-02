<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'permissao',
        'nome_usuario',
        'email_usuario',
        'senha',
        'status_usuario',
    ];

    protected $hidden = [
        'senha',
    ];

    public function locais()
    {
        return $this->belongsToMany(Local::class, 'usuarios_locals');
    }


}
