@extends('layouts.index')

@section('content')
    <style>
        td:hover {
            background-color: #d0d0d0;
            /* Cor de fundo cinza ao passar o mouse */
        }
    </style>

    <div class="container mt-4">

        <h1 class="h3">Lista de Baixas</h1>

        <div class="row">

            <!-- Seção de filtros -->
            <div class="col-md-12">

                <div class="card">

                    <!-- Cards de totais gerais -->
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Filtros para Relatórios de Baixas</h5>
                        </div>

                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('relatorios.filtroRelatorio') }}" id="relatorioForm">


                            <div class="row">
                                <!-- Filtro por local -->
                                <div class="col-md-4">
                                    <label for="local" class="form-label">Local</label>
                                    <select id="local" name="local" class="form-select">
                                        <option value="">Selecione</option>
                                        @foreach ($locals as $local)
                                            <option value="{{ $local->id }}">{{ $local->nome_local }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="estoque" class="form-label">Estoque</label>
                                    <select id="estoque" name="estoque" class="form-select">
                                        <option value="">Selecione</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="defeito_descarte" class="form-label">Motivo Descarte</label>
                                    <select class="form-select" id="defeito_descarte" name="defeito_descarte">
                                        <option value="">Filtre por motivo</option>
                                        <option value="Utilizado">Utilizado</option>
                                        <option value="Vencimento">Vencimento</option>
                                        <option value="Improprio para Consumo">Impróprio para consumo</option>
                                        <option value="Danificado">Danificado</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="categoria-select" class="form-label">Categoria</label>
                                    <select id="categoria" name="categoria" class="form-select">
                                        <option value="">Selecione</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nome_categoria }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row align-items-end">
                                <!-- Filtro por período -->
                                <div class="col-md-2">
                                    <label for="dataInicio" class="form-label">Data Início</label>
                                    <input type="date" id="dataInicio" name="dataInicio" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="dataFim" class="form-label">Data Fim</label>
                                    <input type="date" id="dataFim" name="dataFim" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="formato" class="form-label">Formato</label>
                                    <select id="formato" name="formato" class="form-select">
                                        <option value="">Selecione</option>
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>

                                <input type="hidden" name="baixa" value="true">


                        </form>


                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-secondary w-100" id="baixarBtn">Baixar</button>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="filtrarBtn">Filtrar</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>


    <div class="row">
        <!-- Seção de filtros -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>Lista dos itens filtrados</h5>
                </div>
                <div class="card-body">

                    @if ($filtroRelatorioCategoria != null)
                        @include('layouts.baixas.filtroRelatorioBaixasCategoria', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'categoriaProdutosFiltrados' => $categoriaProdutosFiltrados,
                            'estoque' => $estoque,
                            'escola' => $escola,
                        ])
                    @elseif ($filtroRelatorioEstoque != null)
                        @include('layouts.baixas.filtroRelatorioBaixasEstoque', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'estoque' => $estoque,
                            'escola' => $escola,
                        ])
                    @elseif ($filtroRelatorioLocal != null)
                        @include('layouts.baixas.filtroRelatorioBaixasLocal', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'estoque' => $estoque,
                            'escola' => $escola,
                        ])
                    @elseif ($filtroRelatorioPorLocalECategoria != null)
                        @include('layouts.baixas.filtroRelatorioBaixasPorLocalECategoria', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'estoque' => $estoque,
                            'escola' => $escola,
                        ])
                    @elseif ($filtroRelatorioGeral != null)
                        @include('layouts.baixas.filtroRelatorioBaixasGeral', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'escola' => $escola,
                        ])
                    @elseif ($filtroRelatorioGeralPorCategoria != null)
                        @include('layouts.baixas.filtroRelatorioBaixasGeralPorCategoria', [
                            'produtosFiltrados' => $produtosFiltrados,
                            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
                            'categoria' => $categoria,
                        ])
                    @else
                        <p>Por favor, aplique os filtros para visualizar os resultados.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>


    </div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Captura os botões
        const form = document.getElementById('relatorioForm');
        const baixarBtn = document.getElementById('baixarBtn');
        const filtrarBtn = document.getElementById('filtrarBtn');

        // Função para atualizar a ação do formulário com base no botão clicado
        function setActionAndSubmit(route) {
            form.action = route;
            form.submit();
        }

        // Evento para o botão "Baixar"
        baixarBtn.addEventListener('click', function() {
            const formato = document.getElementById('formato').value;
            if (!formato) {
                alert('Por favor, selecione um formato para baixar.');
                return;
            }
            if (formato === 'pdf') {
                alert('Em breve, essa opção estara disponivel.');
                return;
            }
            setActionAndSubmit('{{ route('relatorios.download.baixas') }}');
        });

        // Evento para o botão "Filtrar"
        filtrarBtn.addEventListener('click', function() {
            setActionAndSubmit('{{ route('relatorios.filtroRelatorio') }}');
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Evento de mudança para o select de local
        document.getElementById('local').addEventListener('change', function() {
            const localId = this.value;
            const estoqueSelect = document.getElementById('estoque');

            // Limpa os estoques existentes
            estoqueSelect.innerHTML = '<option value="">Selecione</option>';

            if (localId) {

                const url = `/escolas/${localId}/estoques/getEstoques`;

                // Faz a requisição para a rota que retorna os estoques
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Adiciona os novos <option> para os estoques
                            data.estoques.forEach(estoque => {
                                const option = document.createElement('option');
                                option.value = estoque.id;
                                option.textContent = estoque.nome_estoque;
                                estoqueSelect.appendChild(option);
                            });
                        } else {
                            console.error('Erro ao carregar estoques:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar estoques:', error);
                    });
            }
        });
    });
</script>
