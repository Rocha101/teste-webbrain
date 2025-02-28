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
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-headset me-2"></i>Sistema de Chamados TI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="views/tickets/new.php"><i class="fas fa-plus-circle me-1"></i> Novo Chamado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/tickets/list.php"><i class="fas fa-list me-1"></i> Meus Chamados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/logout.php"><i class="fas fa-sign-out-alt me-1"></i> Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/register.php"><i class="fas fa-user-plus me-1"></i> Cadastro</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4 text-primary fw-bold">Bem-vindo ao Sistema de Chamados de TI</h2>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h4 class="card-title text-primary mb-3">
                                            <i class="fas fa-info-circle me-2"></i>Como Funciona?
                                        </h4>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Registre-se com seus dados</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Faça login no sistema</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Abra um novo chamado</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Acompanhe o status</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Receba atualizações</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h4 class="card-title text-primary mb-3">
                                            <i class="fas fa-ticket-alt me-2"></i>Tipos de Chamados
                                        </h4>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Problemas técnicos</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Sugestões de melhorias</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Requisições de serviço</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Dúvidas gerais</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="text-center mt-5">
                                <a href="views/auth/login.php" class="btn btn-primary btn-lg me-3 px-4 shadow-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </a>
                                <a href="views/auth/register.php" class="btn btn-outline-primary btn-lg px-4 shadow-sm">
                                    <i class="fas fa-user-plus me-2"></i>Cadastre-se
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0"><i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?> Sistema de Chamados TI - Prefeitura. Todos os direitos reservados.</p>
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