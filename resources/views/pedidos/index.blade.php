@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="h3">Pedidos de Estoque</h1>
        <!-- Botão para adicionar novo pedido -->
        <div class="mb-2">
            <a href="#" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
            <a href="#" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
        </div>


        <!-- Seção de seleção de local -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Filtrar</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="#">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="local" class="form-label">Escola</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione um local</option>
                                @foreach ($locals as $local)
                                    <option value="{{ $local->id }}">{{ $local->nome_local }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="local" class="form-label">Status do Pedido</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione</option>
                                <option value="">Pendente</option>
                                <option value="">Concluido</option>

                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="dataInicio" class="form-label">Data Início</label>
                            <input type="date" id="dataInicio" name="dataInicio" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="dataFim" class="form-label">Data Fim</label>
                            <input type="date" id="dataFim" name="dataFim" class="form-control">
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
                        <!-- Substitua por dados reais -->
                        <tr>
                            <td>1</td>
                            <td>Benjamin</td>
                            <td>Pedido A</td>
                            <td>R$ 1.375,20</td>
                            <td>2025-01-16</td>
                            <td>Pendente</td>
                            <td>
                                <button class="btn btn-sm btn-info"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-ban"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Analides</td>
                            <td>Pedido B</td>
                            <td>R$ 3.418,92</td>
                            <td>2024-12-15</td>
                            <td>Concluído</td>
                            <td>
                                <button class="btn btn-sm btn-info"><i class="fa-regular fa-eye"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Paginação -->
                <nav>
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Próximo</a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
@endsection
