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
    <title>Cadastro - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        html,
        body {
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
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="../../index.php"><i class="fas fa-headset me-2"></i>Sistema de Chamados TI</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i> Cadastro</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="text-primary fw-bold">Cadastro</h2>
                                <p class="text-muted">Preencha os campos abaixo para criar sua conta</p>
                            </div>

                            <div id="alertMessage"></div>

                            <form id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label"><i class="fas fa-user text-primary me-2"></i>Nome Completo</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date" class="form-label"><i class="fas fa-calendar-alt text-primary me-2"></i>Data de Nascimento</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label"><i class="fas fa-phone text-primary me-2"></i>Telefone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control phone-mask" id="phone" name="phone" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="whatsapp" class="form-label"><i class="fab fa-whatsapp text-primary me-2"></i>WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fab fa-whatsapp"></i></span>
                                            <input type="text" class="form-control phone-mask" id="whatsapp" name="whatsapp" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label"><i class="fas fa-envelope text-primary me-2"></i>E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label"><i class="fas fa-lock text-primary me-2"></i>Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label"><i class="fas fa-lock text-primary me-2"></i>Confirmar Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label"><i class="fas fa-map-marker-alt text-primary me-2"></i>Estado</label>
                                        <select class="form-select" id="state" name="state" required>
                                            <option value="">Selecione...</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label"><i class="fas fa-city text-primary me-2"></i>Cidade</label>
                                        <select class="form-select" id="city" name="city" required disabled>
                                            <option value="">Selecione um estado...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Cadastrar
                                    </button>
                                    <a href="login.php" class="btn btn-outline-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Já tem uma conta? Faça login
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="../../index.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Voltar para a página inicial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0"><i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?> Sistema de Chamados TI - Prefeitura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Máscaras para telefone
            $('.phone-mask').mask('(00) 00000-0000');

            // Carregar estados
            $.getJSON('../../includes/location/states.php', function(states) {
                states.forEach(function(state) {
                    $('#state').append(`<option value="${state.uf}">${state.name}</option>`);
                });
            });

            // Carregar cidades quando selecionar estado
            $('#state').change(function() {
                const state = $(this).val();
                if (state) {
                    $('#city').prop('disabled', true);
                    $.getJSON(`../../includes/location/cities.php?state=${state}`, function(cities) {
                        $('#city').empty().append('<option value="">Selecione...</option>');
                        cities.forEach(function(city) {
                            $('#city').append(`<option value="${city.name}">${city.name}</option>`);
                        });
                        $('#city').prop('disabled', false);
                    });
                } else {
                    $('#city').prop('disabled', true).empty().append('<option value="">Selecione um estado...</option>');
                }
            });

            // Submissão do formulário
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                if ($('#password').val() !== $('#confirm_password').val()) {
                    $('#alertMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> As senhas não conferem
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    return;
                }

                $.ajax({
                    url: '../../includes/auth/register_process.php',
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
                            // Redirecionar para verificação de email após 2 segundos
                            setTimeout(function() {
                                window.location.href = 'verify_email.php';
                            }, 2000);
                        } else {
                            $('#alertMessage').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('XHR:', xhr);
                        console.log('Status:', status);
                        console.log('Error:', error);
                        $('#alertMessage').html(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Erro: ${xhr.responseText || 'Tente novamente'}
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