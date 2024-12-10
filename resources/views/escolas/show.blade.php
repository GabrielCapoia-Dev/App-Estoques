@extends('layouts.index')

@section('content')
    <!-- Exibindo erros, caso existam -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulário para vincular um usuário existente -->
    <h3>Vincular Funcionário</h3>
    <form action="{{ route('escolas.vincularUsuario', $local->id) }}" method="POST">
        @csrf
        <div class="col-md-10">
            <div class="mb-2 d-flex align-items-center">
                <!-- Select do Funcionário -->
                <select class="form-control me-2" id="usuario_id" name="usuario_id" required>
                    @foreach ($usuarios as $usuario)
                        @if ($usuario->status_usuario != 'Inativo')
                            <option value="{{ $usuario->id }}">{{ $usuario->nome_usuario }}</option>
                        @endif
                    @endforeach
                </select>

                <!-- Botão de Vincular -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-users-cog"></i></button>
            </div>
        </div>
    </form>
    <br>
    <br>

    <h1 class="h4">Funcionários da Escola: {{ $local->nome_local }}</h1>

    <div class="mb-3">
        <a href="{{ route('escolas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <!-- Tabela para exibir os funcionários -->
    <table class="table table-striped mt-3">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Permissão</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($local->usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nome_usuario }}</td>
                    <td>{{ $usuario->email_usuario }}</td>
                    <td>{{ $usuario->permissao }}</td>
                    <td>{{ $usuario->status_usuario }}</td>
                    <td>
                        <!-- Botão de Editar -->
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning btn-sm"><i
                                class="fa-solid fa-pen"></i></a>

                        <!-- Formulário para desvincular o usuário da escola -->
                        <form action="{{ route('escolas.desvincularUsuario', [$local->id, $usuario->id]) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i
                                    class="fa-solid fa-user-xmark"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
