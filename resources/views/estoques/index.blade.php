@extends('layouts.index')

@section('content')
    <h1 class="h2">Estoques da Escola: {{ $escola->nome_local }}</h1>

    <div class="mb-3">
        <a href="{{ route('escolas.index') }}" class="btn btn-secondary">Voltar para a lista de escolas</a>
        <a href="{{ route('estoques.create', $escola->id) }}" class="btn btn-success">Adicionar Estoque</a>
    </div>

    <!-- Tabela para exibir os estoques -->
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
            @foreach ($estoques as $estoque)
                <tr>
                    <td>{{ $estoque->id }}</td>
                    <td>{{ $estoque->nome_estoque }}</td>
                    <td>{{ $estoque->status_estoque }}</td>
                    <td>{{ $estoque->descricao_estoque }}</td>
                    <td>
                        <a href="{{ route('estoques.show', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('estoques.edit', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-warning btn-sm">Editar</a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
