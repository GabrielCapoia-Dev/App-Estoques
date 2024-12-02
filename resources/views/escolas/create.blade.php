@extends('layouts.index')

@section('content')
    <h1>Cadastrar Escola</h1>

    <form action="{{ route('escolas.store') }}" method="POST">
        @csrf
        {{-- Escola --}}
        <h3>Escola</h3>
        <div class="mb-3">
            <label for="nome_local" class="form-label">Nome da Escola</label>
            <input type="text" class="form-control" id="nome_local" name="nome_local" required>
        </div>
        <div class="mb-3">
            <label for="status_local" class="form-label">Status</label>
            <select class="form-control" id="status_local" name="status_local" required>
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
            </select>
        </div>

        {{-- Endereço --}}
        <h3>Endereço</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="number" class="form-control" id="cep" name="cep" required onblur="buscarCep()" >
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control" id="complemento" name="complemento">
        </div>

        {{-- Estoque --}}
        <h3>Estoque</h3>
        <div class="mb-3">
            <label for="nome_estoque" class="form-label">Nome do Estoque</label>
            <input type="text" class="form-control" id="nome_estoque" name="nome_estoque" required>
        </div>
        <div class="mb-3">
            <label for="status_estoque" class="form-label">Status</label>
            <select class="form-control" id="status_estoque" name="status_estoque" required>
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="descricao_estoque" class="form-label">Descrição do Estoque</label>
            <input type="text" class="form-control" id="descricao_estoque" name="descricao_estoque" required>
        </div>


        <button type="submit" class="btn btn-primary">Cadastrar Escola</button>
    </form>

    {{-- Script para buscar o CEP e preencher os campos --}}
    <script>
        function buscarCep() {
            const cep = document.getElementById('cep').value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cep.length === 8) { // Verifica se o CEP tem 8 caracteres
                const url = `https://viacep.com.br/ws/${cep}/json/`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            // Preenche os campos com as informações retornadas pela API
                            document.getElementById('logradouro').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('cidade').value = data.localidade || '';
                            document.getElementById('estado').value = data.uf || '';
                        } else {
                            alert('CEP não encontrado! Por favor, verifique o CEP.');
                        }
                    })
                    .catch(() => {
                        alert('Erro ao buscar o CEP. Tente novamente.');
                    });
            } else {
                alert('CEP inválido. Por favor, verifique o CEP.');
            }
        }
    </script>
@endsection
