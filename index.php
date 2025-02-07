<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chamados - Prefeitura</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Chamados TI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="views/tickets/new.php">Novo Chamado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/tickets/list.php">Meus Chamados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/register.php">Cadastro</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Bem-vindo ao Sistema de Chamados de TI</h2>
                        <div class="text-center mb-4">
                            <img src="assets/img/support.svg" alt="Suporte TI" class="img-fluid" style="max-width: 300px;">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Como Funciona?</h4>
                                <ul class="list-unstyled">
                                    <li>✓ Registre-se com seus dados</li>
                                    <li>✓ Faça login no sistema</li>
                                    <li>✓ Abra um novo chamado</li>
                                    <li>✓ Acompanhe o status</li>
                                    <li>✓ Receba atualizações</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>Tipos de Chamados</h4>
                                <ul class="list-unstyled">
                                    <li>✓ Problemas técnicos</li>
                                    <li>✓ Sugestões de melhorias</li>
                                    <li>✓ Requisições de serviço</li>
                                    <li>✓ Dúvidas gerais</li>
                                </ul>
                            </div>
                        </div>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="text-center mt-4">
                                <a href="views/auth/login.php" class="btn btn-primary me-2">Login</a>
                                <a href="views/auth/register.php" class="btn btn-outline-primary">Cadastre-se</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© <?php echo date('Y'); ?> Sistema de Chamados TI - Prefeitura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
