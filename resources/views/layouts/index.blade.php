<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link para o Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos para o menu fixo à esquerda em telas grandes */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Ajuste o conteúdo principal para ocupar a área restante */
        .main-content {
            margin-left: 250px;
            /* Espaço para o menu de desktop */
            padding: 20px;
        }

        /* Menu no conteúdo principal, em telas menores */
        @media (max-width: 991px) {
            .desktop-menu {
                display: none;
                /* Ocultar o menu de desktop em telas pequenas */
            }

            .mobile-menu {
                display: block;
                /* Mostrar o menu mobile em telas pequenas */
            }

            .main-content {
                margin-left: 0;
                /* Remover o espaço do menu lateral em telas pequenas */
                padding: 10px;
                /* Reduzir o padding para telas menores */
            }
        }

        /* Menu de desktop */
        @media (min-width: 992px) {
            .desktop-menu {
                display: block;
                /* Mostrar o menu de desktop em telas grandes */
            }

            .mobile-menu {
                display: none;
                /* Ocultar o menu mobile em telas grandes */
            }
        }
    </style>
</head>

<body>
    <!-- Menu de Desktop -->
    <nav class="desktop-menu sidebar">
        <div class="container-fluid">
            <a class="navbar-brand d-block mb-4 text-center" href="{{ url('/') }}">Dashboard</a>
            <ul class="navbar-nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/escolas') }}"><i class="fas fa-school"></i> Escolas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/categorias') }}"><i class="fas fa-th-list"></i> Categorias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/produtos') }}"><i class="fas fa-cogs"></i> Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/baixas') }}"><i class="fas fa-arrow-down"></i> Baixas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/relatorios') }}"><i class="fas fa-chart-line"></i> Relatórios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/usuarios') }}"><i class="fas fa-users"></i> Usuários</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Menu Mobile (Hamburger) -->
    <nav class="mobile-menu navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">MeuSite</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/escolas') }}"><i class="fas fa-school"></i> Escolas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/categorias') }}"><i class="fas fa-th-list"></i>
                            Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/produtos') }}"><i class="fas fa-cogs"></i> Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/baixas') }}"><i class="fas fa-arrow-down"></i> Baixas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/relatorios') }}"><i class="fas fa-chart-line"></i>
                            Relatórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/usuarios') }}"><i class="fas fa-users"></i> Usuários</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <main class="container">
            @yield('content')
        </main>
    </div>

    <!-- Script do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
