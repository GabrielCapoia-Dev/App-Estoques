@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="h3">Pedidos de Estoque</h1>
        <!-- Botão para adicionar novo pedido -->
        <div class="mb-2">
            <a href="{{ route('pedidos.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
        </div>

        <!-- Seção de seleção de filtros -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Filtrar</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pedidos.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="local" class="form-label">Escola</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione um local</option>
                                @foreach ($locals as $local)
                                    <option value="{{ $local->id }}" {{ request('local') == $local->id ? 'selected' : '' }}>
                                        {{ $local->nome_local }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status do Pedido</label>
                            <select id="status" name="status" class="form-select">
                                <option value="">Selecione</option>
                                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="confirmado" {{ request('status') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="dataInicio" class="form-label">Data Início</label>
                            <input type="date" id="dataInicio" name="dataInicio" class="form-control" value="{{ request('dataInicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="dataFim" class="form-label">Data Fim</label>
                            <input type="date" id="dataFim" name="dataFim" class="form-control" value="{{ request('dataFim') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Seção de pedidos -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5>Pedidos</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="col-md-2">Local</th>
                            <th class="col-md-2">Pedido</th>
                            <th class="col-md-2">Valor($)</th>
                            <th class="col-md-2">Data Prevista</th>
                            <th class="col-md-2">Status</th>
                            <th class="col-md-2">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ $pedido->local->nome_local }}</td>
                                <td>{{ $pedido->nome_pedido }}</td>
                                <td>R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pedido->data_prevista)->format('Y-m-d') }}</td>
                                <td>{{ ucfirst($pedido->status) }}</td>
                                <td>
                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info"><i class="fa-regular fa-eye"></i></a>
                                    <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-ban"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginação -->
                {{ $pedidos->links() }}
            </div>
        </div>

    </div>
@endsection
