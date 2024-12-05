@extends('layouts.index')

@section('content')
    <h1>Usuários</h1>

    <div class="mb-3">
        <a href="{{ route('usuarios.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>

        @if (count($usuarios) > 0 && $usuarios->first()->status_usuario === 'Ativo')
            <a href="{{ route('usuarios.index', ['mostrar_inativos' => 'true']) }}" class="btn btn-secondary"><i class="fa-regular fa-eye-slash"></i></a>
        @endif

        @if ((count($usuarios) == 0 ) || (count($usuarios) > 0 && $usuarios->first()->status_usuario === 'Inativo'))
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary"><i class="fa-regular fa-eye"></i></a>
        @endif
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Permissão</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nome_usuario }}</td>
                    <td>{{ $usuario->email_usuario }}</td>
                    <td>{{ $usuario->permissao }}</td>
                    <td class="status" data-id="{{ $usuario->id }}" data-status="{{ $usuario->status_usuario }}">
                        {{ $usuario->status_usuario }}
                    </td>
                    <td>
                        <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
