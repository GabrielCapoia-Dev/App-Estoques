@extends('layouts.index')

@section('content')
    <h1 class="h3">Listagem de Baixas do estoque: {{ $estoque->nome_estoque }}</h1>

    
    <a href="{{ route('estoques.index', ['escola' => $estoque->local->id]) }}" class="btn btn-secondary mt-3">
        <i class="fa-solid fa-arrow-left"></i> Voltar
    </a>
    
    <a href="#" class="btn btn-info mt-3">
        <i class="fa-solid fa-filter"></i> Filtros
    </a>

    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Quantidade Baixa</th>
                <th>Motivo</th>
                <th>Validade</th>
                <th>Data do Descarte</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dadosCompletos as $dado)
                <tr>
                    <td>{{ $dado['id_estoque_produto'] }}</td>
                    <td>{{ $dado['nome_produto'] }}</td>
                    <td>{{ $dado['quantidade_descarte'] }}</td>
                    <td>{{ $dado['defeito_descarte'] }}</td>
                    <td>{{ $dado['validade'] }}</td>
                    <td>{{ $dado['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
