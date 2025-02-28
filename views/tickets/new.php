<?php
require_once '../../config/config.php';
session_start();
redirectIfNotAuthenticated();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-remove {
            color: #dc3545;
            background-color: transparent;
            border: 1px solid #dc3545;
        }

        .btn-remove:hover {
            color: #fff;
            background-color: #dc3545;
        }

        .note-editor {
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">
    <div class="content-wrapper">
        <?php include '../components/navbar.php'; ?>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card rounded-lg">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h2 class="text-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Novo Chamado
                                </h2>
                                <p class="text-muted">Preencha os detalhes do seu chamado abaixo</p>
                            </div>

                            <div id="alertMessage"></div>

                            <form id="ticketForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                                <div class="mb-4">
                                    <label for="incident_type" class="form-label">
                                        <i class="fas fa-tag me-2"></i>Tipo de Incidente
                                    </label>
                                    <select class="form-select" id="incident_type" name="incident_type" required>
                                        <option value="">Selecione o tipo de incidente...</option>
                                        <option value="hardware"><i class="fas fa-desktop"></i> Problema de Hardware</option>
                                        <option value="software"><i class="fas fa-laptop-code"></i> Problema de Software</option>
                                        <option value="network"><i class="fas fa-network-wired"></i> Problema de Rede</option>
                                        <option value="printer"><i class="fas fa-print"></i> Problema de Impressora</option>
                                        <option value="email"><i class="fas fa-envelope"></i> Problema de E-mail</option>
                                        <option value="access"><i class="fas fa-key"></i> Problema de Acesso</option>
                                        <option value="other"><i class="fas fa-question-circle"></i> Outro</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-file-alt me-2"></i>Descrição do Problema
                                    </label>
                                    <textarea id="description" name="description" required></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Descreva detalhadamente o problema, incluindo quando começou e o que já tentou fazer.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-paperclip me-2"></i>Anexos
                                    </label>
                                    <div id="attachmentsContainer">
                                        <div class="attachment-input mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-file"></i>
                                                </span>
                                                <input type="file" class="form-control" name="attachments[]"
                                                    accept="image/*,.pdf,.doc,.docx,.txt">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addAttachment">
                                        <i class="fas fa-plus me-1"></i>Adicionar outro anexo
                                    </button>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-address-book me-2"></i>Contatos para Atendimento
                                    </label>
                                    <div id="contactsContainer">
                                        <div class="contact-input mb-3">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-user"></i>
                                                        </span>
                                                        <input type="text" class="form-control" name="contact_names[]"
                                                            placeholder="Nome do contato" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-phone"></i>
                                                        </span>
                                                        <input type="text" class="form-control phone-mask"
                                                            name="contact_phones[]" placeholder="Telefone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-comment"></i>
                                                        </span>
                                                        <input type="text" class="form-control" name="contact_notes[]"
                                                            placeholder="Observação">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addContact">
                                        <i class="fas fa-plus me-1"></i>Adicionar outro contato
                                    </button>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Abrir Chamado
                                    </button>
                                    <a href="list.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Voltar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">
                <i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?> Sistema de Chamados TI - Prefeitura.
                Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote with custom configuration
            $('#description').summernote({
                height: 200,
                placeholder: 'Descreva detalhadamente o problema...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        alert('Por favor, use o botão de anexos para enviar imagens.');
                    }
                }
            });

            // Phone mask
            $('.phone-mask').mask('(00) 00000-0000');

            // Add attachment
            $('#addAttachment').click(function() {
                const newInput = `
                    <div class="attachment-input mb-2">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-file"></i>
                            </span>
                            <input type="file" class="form-control" name="attachments[]" 
                                   accept="image/*,.pdf,.doc,.docx,.txt">
                            <button type="button" class="btn btn-remove remove-attachment">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#attachmentsContainer').append(newInput);
            });

            // Add contact
            $('#addContact').click(function() {
                const newContact = `
                    <div class="contact-input mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" name="contact_names[]" 
                                           placeholder="Nome do contato" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" class="form-control phone-mask" 
                                           name="contact_phones[]" placeholder="Telefone" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-comment"></i>
                                    </span>
                                    <input type="text" class="form-control" name="contact_notes[]" 
                                           placeholder="Observação">
                                    <button type="button" class="btn btn-remove remove-contact">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#contactsContainer').append(newContact);
                $('.phone-mask').mask('(00) 00000-0000');
            });

            // Remove handlers
            $(document).on('click', '.remove-attachment', function() {
                $(this).closest('.attachment-input').remove();
            });

            $(document).on('click', '.remove-contact', function() {
                $(this).closest('.contact-input').remove();
            });

            // Form submission
            $('#ticketForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('description', $('#description').summernote('code'));

                $.ajax({
                    url: '../../includes/tickets/create_ticket.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#alertMessage').html(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            setTimeout(function() {
                                window.location.href = 'view.php?id=' + response.data.ticket_id;
                            }, 2000);
                        } else {
                            $('#alertMessage').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#alertMessage').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>Erro ao processar a requisição. 
                                Tente novamente.
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