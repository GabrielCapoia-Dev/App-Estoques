@extends('layouts.index')

@section('content')
    <h1>Editar Estoque: {{ $estoque->nome_estoque }}</h1>

    <form action="{{ route('estoques.update', $estoque->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nome_estoque">Nome do Estoque:</label>
            <input type="text" name="nome_estoque" id="nome_estoque" class="form-control" value="{{ old('nome_estoque', $estoque->nome_estoque) }}" required>
            @error('nome_estoque')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao_estoque">Descrição:</label>
            <textarea name="descricao_estoque" id="descricao_estoque" class="form-control" required>{{ old('descricao_estoque', $estoque->descricao_estoque) }}</textarea>
            @error('descricao_estoque')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="status_estoque">Status:</label>
            <select name="status_estoque" id="status_estoque" class="form-control" required>
                <option value="Ativo" {{ $estoque->status_estoque === 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ $estoque->status_estoque === 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status_estoque')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-warning">Atualizar Estoque</button>
        <a href="{{ route('escolas.estoques', $estoque->escola_id) }}" class="btn btn-secondary">Voltar</a>
    </form>
@endsection
