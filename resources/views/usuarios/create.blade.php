{{-- resources/views/usuario/create.blade.php --}}
@extends('layouts.index')

@section('content')
    <h2>Cadastrar Novo Usuário</h2>

    @if(isset($error) && $error)
        <div class="alert alert-danger">
            <strong>Erro!</strong> {{ $message }}
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome_usuario" class="form-label">Nome do Usuário</label>
            <input type="text" class="form-control @error('nome_usuario') is-invalid @enderror" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario') }}">
            @error('nome_usuario')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email_usuario" class="form-label">E-mail do Usuário</label>
            <input type="email" class="form-control @error('email_usuario') is-invalid @enderror" id="email_usuario" name="email_usuario" value="{{ old('email_usuario') }}">
            @error('email_usuario')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control @error('senha') is-invalid @enderror" id="senha" name="senha">
            @error('senha')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="confirmaSenha" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control @error('confirmaSenha') is-invalid @enderror" id="confirmaSenha" name="confirmaSenha">
            @error('confirmaSenha')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="permissao" class="form-label">Permissão</label>
            <select class="form-control @error('permissao') is-invalid @enderror" id="permissao" name="permissao">
                <option value="Administrador">Administrador</option>
                <option value="subAdmin">SubAdmin</option>
                <option value="Gestão">Gestão</option>
                <option value="Secretaria">Secretaria</option>
                <option value="Cozinha">Cozinha</option>
                <option value="Serviços Gerais">Serviços Gerais</option>
            </select>
            @error('permissao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
@endsection
