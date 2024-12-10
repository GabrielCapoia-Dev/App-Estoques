@extends('layouts.index')

@section('content')
    <style>
        td:hover {
            background-color: #d0d0d0;
            /* Cor de fundo cinza ao passar o mouse */
        }
    </style>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Lista de Baixas</h1>

        <!-- Cards de totais gerais -->
        <div class="d-flex justify-content-between align-items-center mt-3">

            <div>
                <a href="" class="btn btn-secondary"><i class="fa-solid fa-file-arrow-down"></i></a>
            </div>

            <!-- Card para valor total -->
            <div class="card p-2"
                style="min-width: 300px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="card-body p-0 d-flex justify-content-between w-100">
                    <div class="text-muted" style="font-size: 0.8rem;">
                        Total Estoque Geral:
                    </div>
                    <div class="text-success" style="font-size: 1rem; font-weight: bold;">
                        R$ {{ $totalEstoqueGeralFormatado }}
                    </div>
                </div>
            </div>

            <div class="card p-2"
                style="min-width: 300px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="card-body p-0 d-flex justify-content-between w-100">
                    <div class="text-muted" style="font-size: 0.8rem;">
                        Total Baixa Geral:
                    </div>
                    <div class="text-danger" style="font-size: 1rem; font-weight: bold;">
                        -R$ {{ $totalBaixaGeralFormatado }}
                    </div>
                </div>
            </div>

        </div>

        <br>

        <!-- Tabela para exibir os resultados -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Valor Total Estoque</th>
                    <th>Valor Total Baixa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultado as $result)
                    <tr>
                        <td style="transition: background-color 0.3s;">
                            <a href="{{ route('estoques.index', $result['idLocal']) }}"
                                style="display: block; text-decoration: none; color: black; padding: 10px;">
                                {{ $result['local'] }}
                            </a>
                        </td>

                        <td class="text-success">R$ {{ number_format($result['valorTotalEstoque'], 2, ',', '.') }}</td>
                        <td class="text-danger">-R$ {{ number_format($result['valorTotalBaixa'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
