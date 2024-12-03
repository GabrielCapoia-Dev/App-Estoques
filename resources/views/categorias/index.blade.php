@extends('layouts.index')

@section('content')
    <h1 class="h2">Lista de Categorias</h1>

    <div class="mb-3">
        <a href="{{ route('categorias.create') }}" class="btn btn-success">Adicionar Categoria</a>
    </div>

    <!-- Tabela para exibir as categorias -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Status</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->id }}</td>
                    <td>{{ $categoria->nome_categoria }}</td>
                    <td>{{ $categoria->status_categoria }}</td>
                    <td>{{ $categoria->descricao_categoria }}</td>
                    <td>
                        <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
