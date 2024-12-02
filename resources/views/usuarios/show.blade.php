@extends('layouts.index')

@section('content')
    <h1>Detalhes do Usuário</h1>

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

    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">Editar</a>
@endsection
