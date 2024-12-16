@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="h3">Pedidos de Estoque</h1>
        <!-- Botão para adicionar novo pedido -->
        <div class="mb-2">
            <a href="#" class="btn btn-success">Novo Pedido</a>
        </div>


        <!-- Seção de seleção de local -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Filtrar Escola</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="#">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="local" class="form-label">Escola</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione um local</option>
                                <!-- Substitua pelos locais reais -->
                                <option value="1">Local 1</option>
                                <option value="2">Local 2</option>
                                <option value="3">Local 3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
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
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Substitua por dados reais -->
                        <tr>
                            <td>1</td>
                            <td>Produto A</td>
                            <td>10</td>
                            <td>2024-12-16</td>
                            <td>Pendente</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Visualizar</button>
                                <button class="btn btn-sm btn-danger">Cancelar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Produto B</td>
                            <td>5</td>
                            <td>2024-12-15</td>
                            <td>Concluído</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Visualizar</button>
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
