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

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .form-control:focus,
        .form-select:focus {
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
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h2 class="card-title">
                                    <i class="fas fa-user-plus me-2"></i>Cadastro
                                </h2>
                                <p class="text-muted">Preencha os campos abaixo para criar sua conta</p>
                            </div>

                            <div id="alertMessage"></div>

                            <form id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label"><i class="fas fa-user me-2"></i>Nome Completo</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Data de Nascimento</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label"><i class="fas fa-phone me-2"></i>Telefone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control phone-mask" id="phone" name="phone" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="whatsapp" class="form-label"><i class="fab fa-whatsapp me-2"></i>WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                            <input type="text" class="form-control phone-mask" id="whatsapp" name="whatsapp" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label"><i class="fas fa-lock me-2"></i>Confirmar Senha</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Estado</label>
                                        <select class="form-select" id="state" name="state" required>
                                            <option value="">Selecione...</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label"><i class="fas fa-city me-2"></i>Cidade</label>
                                        <select class="form-select" id="city" name="city" required disabled>
                                            <option value="">Selecione um estado...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
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