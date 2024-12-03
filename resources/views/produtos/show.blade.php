@extends('layouts.index')

@section('content')
    <h1>Detalhes do Produto: {{ $produto->nome_produto }}</h1>

    <table class="table table-bordered">
        <tr>
            <th>Nome</th>
            <td>{{ $produto->nome_produto }}</td>
        </tr>
        <tr>
            <th>Categoria</th>
            <td>{{ $produto->categoria->nome_categoria }}</td>
        </tr>
        <tr>
            <th>Descrição</th>
            <td>{{ $produto->descricao_produto }}</td>
        </tr>
        <tr>
            <th>Preço</th>
            <td>{{ number_format($produto->preco, 2, ',', '.') }}</td>
        </tr>

    </table>

    <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar para a lista</a>
    <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">Editar Produto</a>
@endsection
