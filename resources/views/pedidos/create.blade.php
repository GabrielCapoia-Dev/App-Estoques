@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="h3">Cadastro de Pedido</h1>

        <!-- Seção de seleção de filtros -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>Filtrar</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pedidos.filtrar') }}" id="relatorioForm">
                    <div class="row">

                        <!-- Filtro por local -->
                        <div class="col-md-6">
                            <label for="local" class="form-label">Local</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione</option>
                                @foreach ($locals as $local)
                                    <option value="{{ $local->id }}">{{ $local->nome_local }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <select id="estoque" name="estoque" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100" id="filtrarBtn">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Seção de filtros -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5>Lista dos itens filtrados</h5>
                        <!-- Total Geral e botão no cabeçalho -->
                        <div class="d-flex align-items-center total-container bg-white text-black rounded-3 shadow-sm p-1">
                            <strong>Valor Total:</strong>
                            <span id="totalGeral" class="ms-3">R$ 0,00</span>
                            <button type="button" class="btn btn-primary ms-3" id="fazerPedidoBtn">Finalizar</button>
                            <button type="button" class="btn ms-3" style="background-color: #11bb69; color: white" id="fazerPedidoBtn"><i class="fa-solid fa-plus"></i> Produto</button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($filtro == null)
                            <p>Por favor, aplique os filtros para visualizar os resultados.</p>
                        @elseif ($filtro != null)
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th>Local</th>
                                            <th>Estoque</th>
                                            <th>Produto</th>
                                            <th>Preço</th>
                                            <th>Qtd Atual</th>
                                            <th>Qtd Minima</th>
                                            <th>Qtd Máxima</th>
                                            <th>Qtd Pedido</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($filtro['produtos_abaixo_quantidade_minima'] as $estoqueProduto)
                                            @if ($estoqueProduto['quantidade_atual'] >= 0)
                                                @php
                                                    // Converte o preço para float
                                                    $preco = (float) str_replace(
                                                        ',',
                                                        '.',
                                                        $estoqueProduto['produto']['preco'],
                                                    );

                                                    // Converte a quantidade atual para float
                                                    $quantidade = (float) $estoqueProduto['quantidade_atual'];
                                                    $quantidadeDiferenca =
                                                        (float) $estoqueProduto['quantidade_diferenca'];

                                                    // Calcula o total
                                                    $total = $preco * $quantidadeDiferenca;
                                                @endphp
                                                <tr>
                                                    <td>{{ $estoqueProduto['local']['nome_local'] }}</td>
                                                    <td>{{ $estoqueProduto['estoque']['nome_estoque'] }}</td>
                                                    <td>{{ $estoqueProduto['produto']['nome_produto'] }}</td>
                                                    <td>R$ {{ number_format($preco, 2, ',', '.') }}</td>
                                                    <td>{{ number_format($quantidade, 0, ',', '.') }}</td>
                                                    <td>{{ $estoqueProduto['quantidade_minima'] ?? 'N/A' }}</td>
                                                    <td>{{ $estoqueProduto['quantidade_maxima'] ?? 'N/A' }}</td>

                                                    <!-- Qtd Pedido com botões de incremento e decremento -->
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <!-- Botão de diminuir -->
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="alterarQuantidade({{ $estoqueProduto['produto']['id'] }}, -1, {{ $preco }})">-</button>

                                                            <!-- Input de quantidade -->
                                                            <input type="number" class="form-control"
                                                                value="{{ $quantidadeDiferenca }}"
                                                                id="quantidade_{{ $estoqueProduto['produto']['id'] }}"
                                                                aria-label="Quantidade" min="0"
                                                                style="width: auto; max-width: 60px; padding: 0.375rem 0.75rem;"
                                                                onchange="atualizarTotal({{ $estoqueProduto['produto']['id'] }}, {{ $preco }})">

                                                            <!-- Botão de aumentar -->
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="alterarQuantidade({{ $estoqueProduto['produto']['id'] }}, 1, {{ $preco }})">+</button>
                                                        </div>
                                                    </td>

                                                    <td id="total_{{ $estoqueProduto['produto']['id'] }}">
                                                        R$ {{ number_format($total, 2, ',', '.') }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    /* Estilo para a div do Total Geral */
    .total-container {
        background-color: #ffffff;
        /* Cor de fundo branco */
        border-radius: 0.5rem;
        /* Bordas arredondadas */
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Sombra interna */
        padding: 12px 25px;
        /* Aumentando o padding para ficar mais largo */
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: auto;
    }

    /* Estilo para o botão suave com cor verde claro */
    .btn-soft-success {
        background-color: #a3dbc0;
        /* Cor verde claro */
        color: #28a745;
        /* Cor de texto */
        border: 1px solid #28a745;
        /* Borda com a mesma cor */
        transition: background-color 0.3s, color 0.3s;
        /* Transição suave */
    }

    .btn-soft-success:hover {
        background-color: #8cd4a3;
        /* Cor verde mais escura ao passar o mouse */
        color: #2c6c2b;
        /* Cor de texto mais escura */
    }

    /* Responsividade */
    @media (max-width: 767px) {
        .total-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .ms-2 {
            margin-left: 0;
        }

        .btn-soft-success {
            width: 100%;
            /* Botão ocupa toda a largura */
        }
    }
</style>

<style>
    /* Remove as setas de incremento e decremento no input de número */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        /* Para Firefox */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Atualizar o total geral ao carregar a página
        atualizarTotalGeral();

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

        // Verifica se já existem valores totais ao carregar a lista de itens
        if (document.querySelectorAll('[id^="total_"]').length > 0) {
            atualizarTotalGeral(); // Atualiza o total geral ao carregar os itens
        }
    });

    // Função para atualizar o total geral da tabela
    function atualizarTotalGeral() {
        let totalGeral = 0;

        // Seleciona todas as células com id começando com "total_"
        const totais = document.querySelectorAll('[id^="total_"]');

        totais.forEach(total => {
            let valor = parseFloat(total.innerText.replace('R$ ', '').replace(',', '.'));

            // Soma o valor total
            if (!isNaN(valor)) {
                totalGeral += valor;
            }
        });

        // Atualiza o total geral no cabeçalho
        document.getElementById('totalGeral').innerText = `R$ ${totalGeral.toFixed(2).replace('.', ',')}`;
    }

    // Função para alterar a quantidade do pedido
    function alterarQuantidade(estoqueProdutoId, incremento, preco) {
        const quantidadeInput = document.getElementById('quantidade_' + estoqueProdutoId);
        let quantidadeAtual = parseFloat(quantidadeInput.value);

        if (isNaN(quantidadeAtual)) {
            quantidadeAtual = 0;
        }

        // Atualiza a quantidade com o incremento
        quantidadeInput.value = Math.max(0, quantidadeAtual + incremento);

        // Atualiza o total após a alteração
        atualizarTotal(estoqueProdutoId, preco);
    }

    // Função para atualizar o total de um item específico
    function atualizarTotal(estoqueProdutoId, preco) {
        const quantidadeInput = document.getElementById('quantidade_' + estoqueProdutoId);
        let quantidadeAtual = parseFloat(quantidadeInput.value);

        // Verifica se a quantidade é negativa
        if (isNaN(quantidadeAtual) || quantidadeAtual < 0) {
            quantidadeAtual = 0; // Se for negativo, reseta para 0
            quantidadeInput.value = 0; // Atualiza o input com o valor 0
        }

        // Calcula o total
        const total = preco * quantidadeAtual;

        // Atualiza o total na tabela
        document.getElementById('total_' + estoqueProdutoId).innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;

        // Atualiza o total geral após a mudança
        atualizarTotalGeral();
    }
</script>
