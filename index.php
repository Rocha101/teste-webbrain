<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chamados - Prefeitura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            padding: 2rem 1rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-body {
            padding: 2rem;
        }

        h2.card-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary);
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--dark);
        }

        .bg-light {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .list-unstyled li {
            font-size: 1rem;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .btn-primary,
        .btn-outline-primary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        footer {
            background-color: var(--dark);
            color: #fff;
            padding: 1.5rem 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            main {
                padding: 1rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            h2.card-title {
                font-size: 1.5rem;
            }

            h4 {
                font-size: 1.1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'views\components\navbar.php'; ?>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Bem-vindo ao Sistema de Chamados de TI</h2>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-light p-3 h-100">
                                    <h4 class="mb-3">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>Como Funciona?
                                    </h4>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Registre-se com seus dados</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Faça login no sistema</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Abra um novo chamado</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Acompanhe o status</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Receba atualizações</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light p-3 h-100">
                                    <h4 class="mb-3">
                                        <i class="fas fa-ticket-alt me-2 text-primary"></i>Tipos de Chamados
                                    </h4>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Problemas técnicos</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Sugestões de melhorias</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Requisições de serviço</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Dúvidas gerais</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="text-center mt-5">
                                <a href="views/auth/login.php" class="btn btn-primary me-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </a>
                                <a href="views/auth/register.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>Cadastre-se
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p class="mb-0"><i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?> Sistema de Chamados TI - Prefeitura</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>