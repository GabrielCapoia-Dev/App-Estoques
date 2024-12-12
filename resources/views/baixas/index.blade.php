@extends('layouts.index')

<!-- Modal para Download -->
<div class="modal fade" id="modalDownload" tabindex="-1" aria-labelledby="modalDownloadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDownloadLabel">Filtros para Download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário para enviar dados -->
                <form id="formDownload" method="GET" action="{{ route('baixas.download') }}">
                    @csrf

                    <!-- Opções de formato -->
                    <div class="mb-3">
                        <label><input type="checkbox" name="formato[]" value="csv"> CSV</label>
                        <label class="ms-3"><input type="checkbox" name="formato[]" value="pdf"> PDF</label>
                    </div>

                    <!-- Lista de Escolas -->
                    <div class="mb-3">
                        <label><input type="checkbox" id="todos" name="todos"> Todas as Escolas</label>
                    </div>

                    <div class="form-group">
                        <label for="escolas" class="form-label">Escolha as Escolas</label>
                        <select name="escolas[]" id="escolas" class="form-control">
                            <option value="">Selecione as escolas</option>
                            @foreach ($locals as $local)
                                <option value="{{ $local->id }}">{{ $local->nome_local }}</option>
                            @endforeach
                        </select>
                        @error('escolas')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Botões de ação -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Baixar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




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
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDownload" class="btn btn-secondary">
                    <i class="fa-solid fa-file-arrow-down"></i>
                </a>
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
        <table class="table table-striped mt-3">
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
                                style="display: block; text-decoration: none; color: black; padding: 4px;">
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

<script>
    document.getElementById('btnDownload').addEventListener('click', function() {
        var formData = new FormData();
        var formatos = [];
        document.querySelectorAll('input[name="formato[]"]:checked').forEach(function(input) {
            formatos.push(input.value);
        });

        formData.append('formato', formatos);
        formData.append('todos', document.getElementById('todos').checked ? 1 : 0);
        document.querySelectorAll('select[name="escolas[]"]:checked').forEach(function(input) {
            formData.append('escolas[]', input.value);
        });

        // Enviar via AJAX para download
        fetch("{{ route('baixas.download') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content')
            },
            body: formData
        }).then(function(response) {
            return response.blob();
        }).then(function(blob) {
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = "relatorio_baixas.csv"; // Ajuste de nome de arquivo conforme o tipo
            link.click();
        }).catch(function(error) {
            console.log('Erro ao baixar o arquivo:', error);
        });
    });
</script>
