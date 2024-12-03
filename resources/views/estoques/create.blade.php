@extends('layouts.index')

@section('content')
    <h1>Criar Estoque para {{ $escola->nome_local }}</h1>

    <form action="{{ route('estoques.criarEstoque', $escola->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nome_estoque">Nome do Estoque:</label>
            <input type="text" name="nome_estoque" id="nome_estoque" class="form-control" value="{{ old('nome_estoque') }}"
                required>
            @error('nome_estoque')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao_estoque">Descrição:</label>
            <textarea name="descricao_estoque" id="descricao_estoque" class="form-control" required>{{ old('descricao_estoque') }}</textarea>
            @error('descricao_estoque')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <br>
        <button type="submit" class="btn btn-success">Salvar Estoque</button>
        <a href="{{ route('estoques.index', $escola->id) }}" class="btn btn-secondary">Voltar</a>

    </form>
@endsection
