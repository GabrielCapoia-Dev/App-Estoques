


@extends('layouts.app')

@section('sidebar')
    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        <form method="POST" action="#">
            @csrf
            <div class="form-group">
                <label for="email_usuario">Email</label>
                <input type="email" id="email_usuario" name="email_usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

    </div>
@endsection

@section('content')
    <div class="gif-container">
        <img src="{{ asset('video/video.gif') }}" alt="Animação" class="gif-background">
    </div>
@endsection

<script>
    async function login() {
        const email = document.getElementById('email').value;
        const senha = document.getElementById('senha').value;

        // Fazer requisição de login
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email_usuario: email,
                senha: senha
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Salvar token e dados do usuário no localStorage
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));

            // Redirecionar ou fazer outra ação após login
            window.location.href = '/home';
        } else {
            alert('Credenciais inválidas');
        }
    }
</script>


<style>
    /* Contêiner do GIF */
    .gif-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 75%;
        /* Ajusta o contêiner para 75% da largura da tela */
        height: 100%;
        z-index: -1;
        display: flex;
        justify-content: center;
        align-items: center;
        /* Centraliza o GIF no contêiner */
    }

    /* Estilo do GIF */
    .gif-background {
        width: 60%;
        height: auto;
    }

    /* Garante que a parte da tela à direita (25%) fique vazia */
    body {
        overflow: hidden;
        /* Remove qualquer barra de rolagem */
    }

    /* Ajusta o conteúdo principal para que a barra lateral não sobreponha o GIF */
    .main-content {
        display: flex;
        justify-content: flex-start;
        /* Faz o conteúdo começar da esquerda */
        width: 100%;
        /* Garante que o conteúdo ocupe toda a largura */
    }
</style>
