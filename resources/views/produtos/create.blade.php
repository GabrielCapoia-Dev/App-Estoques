@extends('layouts.index')

@section('content')
    <h1>Criar Produto</h1>
    <form action="{{ route('produtos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nome_produto">Nome do Produto</label>
            <input type="text" name="nome_produto" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="id_categoria">Categoria</label>
            <select name="id_categoria" class="form-control" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nome_categoria }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="descricao_produto">Descrição</label>
            <textarea name="descricao_produto" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="preco">Preço</label>
            <input type="number" name="preco" class="form-control" required>
        </div>
        <br>
        <button type="submit" class="btn btn-success">Salvar Produto</button>
        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>

    </form>
@endsection

