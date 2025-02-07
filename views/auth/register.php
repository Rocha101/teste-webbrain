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
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Cadastro</h2>
                        <div id="alertMessage"></div>
                        <form id="registerForm">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control phone-mask" id="phone" name="phone" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control phone-mask" id="whatsapp" name="whatsapp" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">Estado</label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Cidade</label>
                                    <select class="form-select" id="city" name="city" required disabled>
                                        <option value="">Selecione um estado...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                                <a href="login.php" class="btn btn-outline-secondary">Já tem uma conta? Faça login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            As senhas não conferem
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
                                    ${response.message}
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
        });
    </script>
</body>
</html>
