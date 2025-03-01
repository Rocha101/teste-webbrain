<?php
require_once '../../config/config.php';

session_start();
redirectIfAuthenticated();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Chamados</title>
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

        .content-wrapper {
            flex: 1;
            padding: 2rem 1rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        h2.card-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary);
        }

        .text-muted {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
        }

        .input-group-text {
            background-color: var(--primary);
            color: #fff;
            border: none;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
        }

        .btn-outline-primary {
            border-radius: 20px;
            padding: 0.3rem 1rem;
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        footer {
            background-color: var(--dark);
            color: #fff;
            padding: 1.5rem 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            h2.card-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h2 class="card-title">
                                    <i class="fas fa-sign-in-alt me-2"></i>Acesso ao Sistema
                                </h2>
                                <p class="text-muted">Entre com suas credenciais para acessar o sistema</p>
                            </div>

                            <div id="alertMessage"></div>

                            <form id="loginForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="mb-3">
                                    <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Sua senha" required>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                    </button>
                                    <a href="register.php" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>Criar nova conta
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="../../index.php" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Voltar para a página inicial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processando...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: '../../includes/auth/login_process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#alertMessage').html(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> Login realizado com sucesso! Redirecionando...
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            setTimeout(function() {
                                window.location.href = '../../index.php';
                            }, 1000);
                        } else {
                            $('#alertMessage').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        $('#alertMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> Erro ao processar a requisição. Tente novamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>