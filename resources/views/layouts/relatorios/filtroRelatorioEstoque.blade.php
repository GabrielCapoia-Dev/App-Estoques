@if ($produtosFiltrados == 'erro')
    <p>Nenhum produto encontrado.</p>
@else
    <div style="overflow-x: auto;">
        <table class="table table-bordered" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th>Escola</th>
                    <th>Estoque</th>
                    <th>Categoria</th>
                    <th>Produto</th>
                    <th>Preço(un)</th>
                    <th>Qtd Atual</th>
                    <th>Qtd Mínima</th>
                    <th>Qtd Máxima</th>
                    <th>Total</th>
                    <th>Validade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estoqueProdutosFiltrados as $estoqueProduto)
                    @php
                        // Converte o preço para float
                        $preco = (float) str_replace(',', '.', $estoqueProduto->produto->preco);

                        // Converte a quantidade atual para float
                        $quantidade = (float) $estoqueProduto->quantidade_atual;

                        // Calcula o total
                        $total = $preco * $quantidade;
                    @endphp
                    <tr>
                        <td>{{ $escola->nome_local }}</td>
                        <td>{{ $estoque->nome_estoque }}</td>
                        <td>{{ $estoqueProduto->produto->categoria->nome_categoria ?? 'Categoria não disponível' }}</td>
                        <td>{{ $estoqueProduto->produto->nome_produto }}</td>
                        <td>R$ {{ number_format($preco, 2, ',', '.') }}</td>
                        <td>{{ number_format($quantidade, 0, ',', '.') }}</td>
                        <td>
                            {{ $estoqueProduto->quantidade_minima ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $estoqueProduto->quantidade_maxima ?? 'N/A' }}
                        </td>
                        <td>R$ {{ number_format($total, 2, ',', '.') }}</td>
                        <td>{{ $estoqueProduto->validade ? \Carbon\Carbon::parse($estoqueProduto->validade)->format('d-m-Y') : 'Sem validade' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>
@endif
