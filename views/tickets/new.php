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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../components/navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Novo Chamado</h2>
                        <div id="alertMessage"></div>
                        <form id="ticketForm">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                            <div class="mb-3">
                                <label for="incident_type" class="form-label">Tipo de Incidente</label>
                                <select class="form-select" id="incident_type" name="incident_type" required>
                                    <option value="">Selecione...</option>
                                    <option value="hardware">Problema de Hardware</option>
                                    <option value="software">Problema de Software</option>
                                    <option value="network">Problema de Rede</option>
                                    <option value="printer">Problema de Impressora</option>
                                    <option value="email">Problema de E-mail</option>
                                    <option value="access">Problema de Acesso</option>
                                    <option value="other">Outro</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição do Problema</label>
                                <textarea id="description" name="description" required></textarea>
                                <div class="form-text">Descreva detalhadamente o problema, incluindo quando começou e o que já tentou fazer.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Anexos</label>
                                <div id="attachmentsContainer">
                                    <div class="attachment-input mb-2">
                                        <input type="file" class="form-control" name="attachments[]" accept="image/*,.pdf,.doc,.docx,.txt">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="addAttachment">
                                    + Adicionar outro anexo
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contatos para Atendimento</label>
                                <div id="contactsContainer">
                                    <div class="contact-input mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="contact_names[]" placeholder="Nome do contato" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control phone-mask" name="contact_phones[]" placeholder="Telefone" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="contact_notes[]" placeholder="Observação">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="addContact">
                                    + Adicionar outro contato
                                </button>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Abrir Chamado</button>
                                <a href="list.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Summernote
            $('#description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Máscaras para telefone
            $('.phone-mask').mask('(00) 00000-0000');

            // Adicionar mais anexos
            $('#addAttachment').click(function() {
                const newInput = `
                    <div class="attachment-input mb-2">
                        <div class="input-group">
                            <input type="file" class="form-control" name="attachments[]" accept="image/*,.pdf,.doc,.docx,.txt">
                            <button type="button" class="btn btn-outline-danger remove-attachment">
                                <i class="bi bi-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                `;
                $('#attachmentsContainer').append(newInput);
            });

            // Remover anexo
            $(document).on('click', '.remove-attachment', function() {
                $(this).closest('.attachment-input').remove();
            });

            // Adicionar mais contatos
            $('#addContact').click(function() {
                const newContact = `
                    <div class="contact-input mb-2">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_names[]" placeholder="Nome do contato" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control phone-mask" name="contact_phones[]" placeholder="Telefone" required>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="contact_notes[]" placeholder="Observação">
                                    <button type="button" class="btn btn-outline-danger remove-contact">
                                        <i class="bi bi-trash"></i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#contactsContainer').append(newContact);
                $('.phone-mask').mask('(00) 00000-0000');
            });

            // Remover contato
            $(document).on('click', '.remove-contact', function() {
                $(this).closest('.contact-input').remove();
            });

            // Submissão do formulário
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
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `);
                            setTimeout(function() {
                                window.location.href = 'view.php?id=' + response.data.ticket_id;
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
