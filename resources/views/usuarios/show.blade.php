@extends('layouts.index')

@section('content')
    <h1 class="h2">Detalhes do Usuário</h1>

    <table class="table table-bordered">
        <tr>
            <th>Nome</th>
            <td>{{ $usuario->nome_usuario }}</td>
        </tr>
        <tr>
            <th>E-mail</th>
            <td>{{ $usuario->email_usuario }}</td>
        </tr>
        <tr>
            <th>Permissão</th>
            <td>{{ $usuario->permissao }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $usuario->status_usuario }}</td>
        </tr>
    </table>
    <h2>Escolas</h2>

    <ul>
        @foreach ($locais as $local)
            <li>{{ $local->nome_local }}</li>
        @endforeach
    </ul>

    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
    <a href="{{ route('usuarios.index', $usuario->id) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
@endsection
