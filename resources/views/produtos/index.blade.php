@extends('layouts.index')

@section('content')
    <h1>Produtos</h1>
    <a href="{{ route('produtos.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
    <table class="table">
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
            @foreach($produtos as $produto)
                <tr>
                    <td>{{ $produto->id }}</td>
                    <td>{{ $produto->nome_produto }}</td>
                    <td>{{ $produto->preco }}</td>
                    <td>{{ $produto->categoria->nome_categoria }}</td>
                    <td>{{ $produto->status_produto }}</td>
                    <td>
                        <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
