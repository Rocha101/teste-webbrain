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
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Verificação de E-mail</h2>
                        <div id="alertMessage"></div>
                        <p class="text-center">
                            Um código de verificação foi enviado para:<br>
                            <strong><?php echo htmlspecialchars($_SESSION['verification_email']); ?></strong>
                        </p>
                        <form id="verificationForm">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['verification_email']); ?>">
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">Código de Verificação</label>
                                <input type="text" class="form-control" id="code" name="code" required maxlength="6" pattern="\d{6}">
                                <div class="form-text">Digite o código de 6 dígitos enviado ao seu e-mail.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Verificar</button>
                                <button type="button" id="resendCode" class="btn btn-outline-secondary">Reenviar Código</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 2000);
                        } else {
                            $('#alertMessage').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#alertMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Erro ao processar a requisição. Tente novamente.
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
                                ${response.message}
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
