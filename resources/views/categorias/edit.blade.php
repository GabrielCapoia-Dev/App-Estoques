@extends('layouts.index')

@section('content')
    <h1 class="h2">Editar Categoria: {{ $categoria->nome_categoria }}</h1>

    <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nome_categoria">Nome da Categoria:</label>
            <input type="text" name="nome_categoria" id="nome_categoria" class="form-control"
                value="{{ old('nome_categoria', $categoria->nome_categoria) }}" required>
            @error('nome_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao_categoria">Descrição:</label>
            <textarea name="descricao_categoria" id="descricao_categoria" class="form-control" required>{{ old('descricao_categoria', $categoria->descricao_categoria) }}</textarea>
            @error('descricao_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="status_categoria">Status:</label>
            <select name="status_categoria" id="status_categoria" class="form-control" required>
                <option value="Ativo"
                    {{ old('status_categoria', $categoria->status_categoria) === 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo"
                    {{ old('status_categoria', $categoria->status_categoria) === 'Inativo' ? 'selected' : '' }}>Inativo
                </option>
            </select>
            @error('status_categoria')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <br>
        <button type="submit" class="btn btn-warning">Atualizar Categoria</button>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
@endsection
