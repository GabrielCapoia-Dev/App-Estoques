@extends('layouts.index')

@section('content')
    <h1>Detalhes do Estoque: {{ $estoque->nome_estoque }}</h1>

    <!-- Tabela de produtos no estoque -->
    <h3>Produtos no Estoque</h3>

    <!-- Botão para adicionar um novo produto ao estoque -->
    <a href="{{ route('estoques.produtos.create', $estoque->id) }}" class="btn btn-success mt-3">Adicionar Produto</a>

    <!-- Botões de navegação -->
    <a href="{{ route('estoques.index', ['escola' => $estoque->local->id]) }}" class="btn btn-secondary mt-3">Voltar</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome do Produto</th>
                <th>Quantidade Atual</th>
                <th>Quantidade Mínima</th>
                <th>Quantidade Máxima</th>
                <th>Validade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estoque->produtos as $produto)
                <tr>
                    <td>{{ $produto->nome_produto }}</td>
                    <td>{{ $produto->pivot->quantidade_atual }}</td>
                    <td>{{ $produto->pivot->quantidade_minima }}</td>
                    <td>{{ $produto->pivot->quantidade_maxima }}</td>
                    <td>{{ $produto->pivot->validade }}</td>
                    <td>
                        <!-- Botões para editar e dar baixa no produto -->
                        <a href="{{ route('estoques.produtos.edit', ['estoque' => $estoque->id, 'produto' => $produto->id]) }}"
                            class="btn btn-warning btn-sm">Editar</a>
                        <a href="{{ route('estoques.produtos.baixa', ['estoque' => $estoque->id, 'produto' => $produto->id]) }}"
                            class="btn btn-danger btn-sm">Dar Baixa</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
