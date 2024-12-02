@extends('layouts.index')

@section('content')
    <h1>Editar Escola</h1>

    <form action="{{ route('escolas.update', $local->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Escola --}}
        <h3>Escola</h3>
        <div class="mb-3">
            <label for="nome_local" class="form-label">Nome da Escola</label>
            <input type="text" class="form-control" id="nome_local" name="nome_local" value="{{ old('nome_local', $local->nome_local) }}" required>
            @error('nome_local')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status_local" class="form-label">Status</label>
            <select class="form-control" id="status_local" name="status_local" required>
                <option value="Ativo" {{ old('status_local', $local->status_local) == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ old('status_local', $local->status_local) == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status_local')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Endereço --}}
        <h3>Endereço</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="{{ old('cep', $local->endereco->cep) }}" required>
                    @error('cep')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero" value="{{ old('numero', $local->endereco->numero) }}" required>
                    @error('numero')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="{{ old('logradouro', $local->endereco->logradouro) }}" required>
                    @error('logradouro')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="{{ old('bairro', $local->endereco->bairro) }}" required>
                    @error('bairro')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="{{ old('cidade', $local->endereco->cidade) }}" required>
                    @error('cidade')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" value="{{ old('estado', $local->endereco->estado) }}" required>
                    @error('estado')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control" id="complemento" name="complemento" value="{{ old('complemento', $local->endereco->complemento) }}">
            @error('complemento')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Escola</button>
    </form>
@endsection
