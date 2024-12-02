@extends('layouts.index')

@section('content')
    <h1>Detalhes do Estoque: {{ $estoque->nome_estoque }}</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nome:</h5>
            <p class="card-text">{{ $estoque->nome_estoque }}</p>

            <h5 class="card-title">Descrição:</h5>
            <p class="card-text">{{ $estoque->descricao_estoque }}</p>

            <h5 class="card-title">Status:</h5>
            <p class="card-text">{{ $estoque->status_estoque }}</p>
        </div>
    </div>

    <a href="{{ route('escolas.estoques', $estoque->escola_id) }}" class="btn btn-secondary mt-3">Voltar</a>
    <a href="{{ route('estoques.edit', $estoque->id) }}" class="btn btn-warning mt-3">Editar</a>
@endsection
