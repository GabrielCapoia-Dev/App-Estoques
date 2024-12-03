@extends('layouts.index')

@section('content')
    <h1 class="h2">Detalhes da Categoria: {{ $categoria->nome_categoria }}</h1>

    <div class="mb-3">
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Voltar para a lista de categorias</a>
        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning">Editar</a>
    </div>

    <table class="table">
        <tr>
            <th>ID</th>
            <td>{{ $categoria->id }}</td>
        </tr>
        <tr>
            <th>Nome</th>
            <td>{{ $categoria->nome_categoria }}</td>
        </tr>
        <tr>
            <th>Descrição</th>
            <td>{{ $categoria->descricao_categoria }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $categoria->status_categoria }}</td>
        </tr>
    </table>
@endsection
