@extends('layouts.index')

@section('content')
    <h1 class="h2">Criar Nova Categoria</h1>

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nome_categoria">Nome da Categoria:</label>
            <input type="text" name="nome_categoria" id="nome_categoria" class="form-control"
                value="{{ old('nome_categoria') }}" required>
            @error('nome_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao_categoria">Descrição:</label>
            <textarea name="descricao_categoria" id="descricao_categoria" class="form-control" required>{{ old('descricao_categoria') }}</textarea>
            @error('descricao_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="status_categoria">Status:</label>
            <select name="status_categoria" id="status_categoria" class="form-control" required>
                <option value="Ativo" {{ old('status_categoria') === 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ old('status_categoria') === 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <br>
        <button type="submit" class="btn btn-success">Criar Categoria</button>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
@endsection
