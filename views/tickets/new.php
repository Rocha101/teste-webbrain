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

        h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary);
        }

        .text-muted {
            color: var(--secondary);
        }

        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .input-group-text {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: var(--secondary);
        }

        .form-text {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .btn-primary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-outline-secondary,
        .btn-outline-primary {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .btn-remove {
            color: var(--danger);
            border: 1px solid var(--danger);
            border-radius: 8px;
            padding: 0.3rem 0.8rem;
        }

        .note-editor {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .list-group-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .btn-primary,
            .btn-outline-secondary,
            .btn-outline-primary {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .row.g-2>div {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h2><i class="fas fa-plus-circle me-2"></i>Novo Chamado</h2>
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
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="attachmentInput" name="attachments[]"
                                            multiple accept="image/*,.pdf,.doc,.docx,.txt">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Você pode selecionar múltiplos arquivos de uma vez.
                                        </div>
                                    </div>
                                    <div id="attachmentList" class="list-group mb-3" style="display: none;"></div>
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
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                        <input type="text" class="form-control" name="contact_names[]"
                                                            placeholder="Nome do contato" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        <input type="text" class="form-control phone-mask" name="contact_phones[]"
                                                            placeholder="Telefone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
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
                                    <button type="submit" class="btn btn-primary">
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

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#description').summernote({
                height: 200,
                placeholder: 'Descreva detalhadamente o problema...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol']]
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        alert('Por favor, use o botão de anexos para enviar imagens.');
                    }
                }
            });

            $('.phone-mask').mask('(00) 00000-0000');

            $('#attachmentInput').on('change', function(e) {
                const files = e.target.files;
                const $attachmentList = $('#attachmentList');
                $attachmentList.empty();
                if (files.length > 0) {
                    $attachmentList.show();
                    $.each(files, function(index, file) {
                        const fileItem = `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-file me-2"></i>${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-attachment" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        $attachmentList.append(fileItem);
                    });
                } else {
                    $attachmentList.hide();
                }
            });

            $(document).on('click', '.remove-attachment', function() {
                const index = $(this).data('index');
                const input = document.getElementById('attachmentInput');
                const files = Array.from(input.files);
                files.splice(index, 1);
                const dataTransfer = new DataTransfer();
                files.forEach(file => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
                $(input).trigger('change');
            });

            $('#addContact').click(function() {
                const newContact = `
                    <div class="contact-input mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="contact_names[]" placeholder="Nome do contato" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control phone-mask" name="contact_phones[]" placeholder="Telefone" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <input type="text" class="form-control" name="contact_notes[]" placeholder="Observação">
                                    <button type="button" class="btn btn-outline-danger remove-contact"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#contactsContainer').append(newContact);
                $('.phone-mask').mask('(00) 00000-0000');
            });

            $(document).on('click', '.remove-contact', function() {
                $(this).closest('.contact-input').remove();
            });

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
                            setTimeout(() => window.location.href = 'view.php?id=' + response.data.ticket_id, 2000);
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
                                <i class="fas fa-exclamation-triangle me-2"></i>Erro ao processar a requisição. Tente novamente.
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