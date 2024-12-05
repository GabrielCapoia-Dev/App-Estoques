{{-- resources/views/usuario/edit.blade.php --}}
@extends('layouts.index')

@section('content')
    <h2>Editar Usuário</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Erro!</strong> Verifique os erros abaixo:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome_usuario" class="form-label">Nome do Usuário</label>
            <input type="text" class="form-control" id="nome_usuario" name="nome_usuario"
                value="{{ old('nome_usuario', $usuario->nome_usuario) }}" oninput="validarNomeUsuario()" required>
            <div id="nome-feedback" class="form-text"></div>
        </div>

        <div class="mb-3">
            <label for="email_usuario" class="form-label">E-mail do Usuário</label>
            <input type="email" class="form-control" id="email_usuario" name="email_usuario"
                value="{{ old('email_usuario', $usuario->email_usuario) }}" oninput="validarEmailUsuario()" required>
            <div id="email-feedback" class="form-text"></div>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha (opcional)</label>
            <input type="password" class="form-control" id="senha" name="senha" oninput="validarSenha()">
            <div id="senha-feedback" class="form-text"></div>
        </div>

        <div class="mb-3">
            <label for="confirmaSenha" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha"
                oninput="validarConfirmaSenha()">
            <div id="confirma-feedback" class="form-text"></div>
        </div>

        <div class="mb-3">
            <label for="status_usuario" class="form-label">Status</label>
            <select class="form-control" id="status_usuario" name="status_usuario" required>
                <option value="Ativo" {{ $usuario->status_usuario == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ $usuario->status_usuario == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="permissao" class="form-label">Permissão</label>
            <select class="form-control @error('permissao') is-invalid @enderror" id="permissao" name="permissao">
                <option value="Administrador"
                    {{ old('permissao', $usuario->permissao) == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="subAdmin" {{ old('permissao', $usuario->permissao) == 'subAdmin' ? 'selected' : '' }}>
                    SubAdmin</option>
                <option value="Gestão" {{ old('permissao', $usuario->permissao) == 'Gestão' ? 'selected' : '' }}>Gestão
                </option>
                <option value="Secretaria" {{ old('permissao', $usuario->permissao) == 'Secretaria' ? 'selected' : '' }}>
                    Secretaria</option>
                <option value="Cozinha" {{ old('permissao', $usuario->permissao) == 'Cozinha' ? 'selected' : '' }}>Cozinha
                </option>
                <option value="Serviços Gerais"
                    {{ old('permissao', $usuario->permissao) == 'Serviços Gerais' ? 'selected' : '' }}>Serviços Gerais
                </option>
            </select>
            @error('permissao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
    <a href="{{ route('usuarios.index', $usuario->id) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>

    </form>
@endsection

<script>
    // Valida a senha com os critérios fornecidos
    function validarSenha() {
        const senha = document.getElementById("senha").value;
        const feedback = document.getElementById("senha-feedback");

        const regras = [{
                regex: /.{7,16}/,
                mensagem: "A senha deve ter entre 7 e 16 caracteres."
            },
            {
                regex: /[A-Z]/,
                mensagem: "A senha deve conter ao menos uma letra maiúscula."
            },
            {
                regex: /[a-z]/,
                mensagem: "A senha deve conter ao menos uma letra minúscula."
            },
            {
                regex: /[0-9]/,
                mensagem: "A senha deve conter ao menos um número."
            },
            {
                regex: /[@$!%*?&]/,
                mensagem: "A senha deve conter ao menos um caractere especial (@$!%*?&)."
            }
        ];

        let mensagens = [];

        regras.forEach(regra => {
            if (!regra.regex.test(senha)) {
                mensagens.push(regra.mensagem);
            }
        });

        if (mensagens.length === 0) {
            feedback.textContent = "A senha atende a todos os requisitos.";
            feedback.className = "form-text text-success";
        } else {
            feedback.textContent = mensagens.join(" ");
            feedback.className = "form-text text-danger";
        }
    }

    // Valida se a senha e a confirmação coincidem
    function validarConfirmaSenha() {
        const senha = document.getElementById("senha").value;
        const confirmaSenha = document.getElementById("confirmaSenha").value;
        const feedback = document.getElementById("confirma-feedback");

        if (confirmaSenha === senha) {
            feedback.textContent = "As senhas coincidem.";
            feedback.className = "form-text text-success";
        } else {
            feedback.textContent = "As senhas não coincidem.";
            feedback.className = "form-text text-danger";
        }
    }

    // Valida o nome do usuário
    function validarNomeUsuario() {
        const nome = document.getElementById("nome_usuario").value;
        const feedback = document.getElementById("nome-feedback");

        if (nome.length < 5) {
            feedback.textContent = "O nome deve ter no mínimo 5 caracteres.";
            feedback.className = "form-text text-danger";
        } else if (nome.length > 30) {
            feedback.textContent = "O nome deve ter no máximo 30 caracteres.";
            feedback.className = "form-text text-danger";
        } else {
            feedback.textContent = "O nome está válido.";
            feedback.className = "form-text text-success";
        }
    }

    // Valida o e-mail do usuário
    function validarEmailUsuario() {
        const email = document.getElementById("email_usuario").value;
        const feedback = document.getElementById("email-feedback");
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regex para validar e-mails

        if (emailRegex.test(email)) {
            feedback.textContent = "O e-mail está válido.";
            feedback.className = "form-text text-success";
        } else {
            feedback.textContent = "Insira um e-mail válido.";
            feedback.className = "form-text text-danger";
        }
    }
</script>
