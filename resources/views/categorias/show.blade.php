{{-- resources/views/categorias/show.blade.php --}}
@extends('layouts.index')

@section('content')
    <h1 class="h2">Detalhes da Categoria: {{ $categoria->nome_categoria }}</h1>

    <!-- Tabela de produtos da categoria -->
    <h3>Produtos na Categoria</h3>

    <div class="mb-3">
        
        <!-- Botão para adicionar um novo produto à categoria -->
        <a href="{{ route('categorias.produtos.create', $categoria->id) }}" class="btn btn-success mt-3"><i
                class="fa-solid fa-plus"></i> Novo</a>

        <!-- Botões de navegação -->
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary mt-3"><i class="fa-solid fa-arrow-left"></i>
            Voltar</a>

    </div>

    <table class="table table-striped mt-3">

        <thead>
            <tr>
                <th>Nome do Produto</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categoria->produtos as $produto)
                <tr>
                    <td>{{ $produto->nome_produto }}</td>
                    <td>{{ $produto->descricao_produto }}</td>
                    <td>{{ $produto->preco }}</td>
                    <td>{{ $produto->status_produto }}</td>
                    <td>
                        <!-- Botões para editar o produto -->
                        <a href="{{ route('categorias.produtos.edit', ['categoria' => $categoria->id, 'produto' => $produto->id]) }}"
                            class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Editar</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
