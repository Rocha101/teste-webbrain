<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['verification_email'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de E-mail - Sistema de Chamados</title>
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

        .btn-outline-secondary {
            border-radius: 20px;
            padding: 0.3rem 1rem;
            color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-text {
            color: var(--secondary);
            font-size: 0.85rem;
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
    <div class="content-wrapper">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h2 class="card-title">
                                    <i class="fas fa-key me-2"></i>Verificação de E-mail
                                </h2>
                                <p class="text-muted">Digite o código enviado para <strong><?php echo htmlspecialchars($_SESSION['verification_email']); ?></strong></p>
                            </div>
                            <div id="alertMessage"></div>
                            <form id="verificationForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['verification_email']); ?>">
                                <div class="mb-3">
                                    <label for="code" class="form-label"><i class="fas fa-key me-2"></i>Código de Verificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control" id="code" name="code" required maxlength="6" pattern="\d{6}" placeholder="Digite o código">
                                    </div>
                                    <div class="form-text">Código de 6 dígitos enviado ao seu e-mail.</div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check-circle me-2"></i>Verificar
                                    </button>
                                    <button type="button" id="resendCode" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo-alt me-2"></i>Reenviar Código
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Voltar para login
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
            $('#verificationForm').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verificando...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: '../../includes/auth/verify_email_process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#alertMessage').html(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            setTimeout(function() { window.location.href = 'login.php'; }, 2000);
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

            $('#resendCode').on('click', function() {
                const resendBtn = $(this);
                const originalText = resendBtn.html();
                resendBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Enviando...');
                resendBtn.prop('disabled', true);

                $.ajax({
                    url: '../../includes/auth/resend_verification_code.php',
                    type: 'POST',
                    data: {
                        email: $('input[name="email"]').val(),
                        csrf_token: $('input[name="csrf_token"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#alertMessage').html(`
                            <div class="alert alert-${response.success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                                <i class="fas fa-envelope me-2"></i> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        resendBtn.html(originalText);
                        resendBtn.prop('disabled', false);
                    },
                    error: function() {
                        $('#alertMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> Erro ao reenviar o código. Tente novamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        resendBtn.html(originalText);
                        resendBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>