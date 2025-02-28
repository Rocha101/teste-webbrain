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
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body class="bg-light">
    <div class="content-wrapper">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="text-primary fw-bold">Verificação de E-mail</h2>
                                <p class="text-muted">Digite o código enviado para <strong><?php echo htmlspecialchars($_SESSION['verification_email']); ?></strong></p>
                            </div>
                            <div id="alertMessage"></div>
                            <form id="verificationForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['verification_email']); ?>">
                                <div class="mb-3">
                                    <label for="code" class="form-label"><i class="fas fa-key text-primary me-2"></i>Código de Verificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-key"></i></span>
                                        <input type="text" class="form-control" id="code" name="code" required maxlength="6" pattern="\d{6}" placeholder="Digite o código">
                                    </div>
                                    <div class="form-text">Código de 6 dígitos enviado ao seu e-mail.</div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
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
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Voltar para login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0"><i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?> Sistema de Chamados TI - Todos os direitos reservados.</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#verificationForm').on('submit', function(e) {
                e.preventDefault();
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
                        }
                    },
                    error: function() {
                        $('#alertMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> Erro ao processar a requisição. Tente novamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                    }
                });
            });
            $('#resendCode').on('click', function() {
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
                    }
                });
            });
        });
    </script>
</body>
</html>