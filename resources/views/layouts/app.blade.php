<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* Estilos gerais */
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        /* Barra lateral à direita */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 25%;
            background: linear-gradient(180deg, #add8e6, #98fb98);
            /* Gradiente de azul claro para verde claro */
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Container para o formulário de login */
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-container input {
            margin-bottom: 15px;
        }

        /* Estilos do formulário de login */
        .main-content {
            padding-left: 10px;
            padding-right: 10px;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Estilos do Rodapé */
        .footer {
            background-color: #f8f9fa;
            padding: 10px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Conteúdo Principal (Tela de Login) -->
    <div class="main-content">
        <div class="sidebar">
            @yield('sidebar')
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>
    <!-- Rodapé -->
    <div class="footer text-center"
        style="height: 15px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1); font-size: 0.8rem;">
        <p style="margin-top: 20px;">© 2024 Desenvolvido por <a href="https://github.com/GabrielCapoia-Dev"
                target="_blank">Gabriel Capoia</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
