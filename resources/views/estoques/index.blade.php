@extends('layouts.index')

@section('content')
    <h1 class="h2">Estoques da Escola: {{ $escola->nome_local }}</h1>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <a href="{{ route('escolas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
            <a href="{{ route('estoques.create', $escola->id) }}" class="btn btn-success"><i class="fa-solid fa-plus"></i>
                Novo</a>
            <a href="{{ route('baixas.index') }}" class="btn btn-danger">
                <i class="fa-solid fa-recycle"></i></i>
                Baixas</a>

        </div>
        <!-- Card para valor total -->
        <div class="card p-2"
            style="min-width: 300px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body p-0 d-flex justify-content-between w-100">
                <div class="text-muted" style="font-size: 0.8rem;">
                    Total Estoque:
                </div>
                <div class="text-success" style="font-size: 1rem; font-weight: bold;">
                    R$ {{ $totalEstoque ?? '0,00' }}
                </div>
            </div>
        </div>

        <div class="card p-2"
            style="min-width: 300px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body p-0 d-flex justify-content-between w-100">
                <div class="text-muted" style="font-size: 0.8rem;">
                    Total Baixa:
                </div>
                <div class="text-danger" style="font-size: 1rem; font-weight: bold;">
                    -R$ {{ $totalBaixa ?? '0,00' }}
                </div>
            </div>
        </div>

    </div>
    <br>

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
                            class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('estoques.edit', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></i></a>
                        <a href="{{ route('estoques.baixas.show', $estoque->id) }}" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-recycle"></i></i></a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
