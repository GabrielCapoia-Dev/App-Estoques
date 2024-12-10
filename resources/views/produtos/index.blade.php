@extends('layouts.index')

@section('content')
    <style>
        .bg-td:hover {
            background-color: #d0d0d0;
            /* Cor de fundo cinza ao passar o mouse */
        }
    </style>
    <h1 class="h2">Produtos: {{ $produtos->first()->status_produto ?? 'Inativo' }}</h1>

    <div class="mb-3">
        <a href="{{ route('produtos.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>

        @if (count($produtos) > 0 && $produtos->first()->status_produto === 'Ativo')
            <a href="{{ route('produtos.index', ['mostrar_inativos' => 'true']) }}" class="btn btn-secondary"><i
                    class="fa-regular fa-eye-slash"></i></a>
        @endif

        @if (count($produtos) == 0 || (count($produtos) > 0 && $produtos->first()->status_produto === 'Inativo'))
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary"><i class="fa-regular fa-eye"></i></a>
        @endif
    </div>

    <table class="table table-striped mt-3">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço R$</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produtos as $produto)
                <tr>
                    <td>{{ $produto->id }}</td>

                    <td class="bg-td" style="transition: background-color 0.3s;">
                        <a href="{{ route('produtos.show', $produto->id) }}"
                            style="display: block; text-decoration: none; color: black;">
                            {{ $produto->nome_produto }}
                        </a>
                    </td>

                    <td>{{ $produto->preco }}</td>


                    <td class="bg-td" style="transition: background-color 0.3s;">
                        <a href="{{ route('categorias.show', $produto->categoria->id) }}"
                            style="display: block; text-decoration: none; color: black;">
                            {{ $produto->categoria->nome_categoria }}
                        </a>
                    </td>
                    <td>{{ $produto->status_produto }}</td>

                    <td>
                        <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-info btn-sm"><i
                                class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning btn-sm"><i
                                class="fa-solid fa-pen-to-square"></i></a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
