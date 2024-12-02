@extends('layouts.index')

@section('content')
    <h1>Editar Usuário</h1>

    @if (isset($error))
        <div class="alert alert-danger">{{ $message }}</div>
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif

    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome_usuario" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario', $usuario->nome_usuario) }}" required>
        </div>

        <div class="mb-3">
            <label for="email_usuario" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email_usuario" name="email_usuario" value="{{ old('email_usuario', $usuario->email_usuario) }}" required>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha">
        </div>

        <div class="mb-3">
            <label for="confirmaSenha" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha">
        </div>

        <div class="mb-3">
            <label for="permissao" class="form-label">Permissão</label>
            <select class="form-control" id="permissao" name="permissao">
                <option value="Administrador" {{ $usuario->permissao == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="subAdmin" {{ $usuario->permissao == 'subAdmin' ? 'selected' : '' }}>Sub Administrador</option>
                <option value="Gestão" {{ $usuario->permissao == 'Gestão' ? 'selected' : '' }}>Gestão</option>
                <option value="Secretaria" {{ $usuario->permissao == 'Secretaria' ? 'selected' : '' }}>Secretaria</option>
                <option value="Cozinha" {{ $usuario->permissao == 'Cozinha' ? 'selected' : '' }}>Cozinha</option>
                <option value="Serviços Gerais" {{ $usuario->permissao == 'Serviços Gerais' ? 'selected' : '' }}>Serviços Gerais</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status_usuario" class="form-label">Status</label>
            <select class="form-control" id="status_usuario" name="status_usuario" required>
                <option value="Ativo" {{ $usuario->status_usuario == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ $usuario->status_usuario == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-warning">Atualizar</button>
        </div>
    </form>
@endsection
