<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

// Restrict to admins only
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: list.php'); // Redirect non-admins to their ticket list
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$ticket_id = (int)$_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Admins can view any ticket, no user_id restriction
    $stmt = $db->prepare("
        SELECT t.*, u.full_name as user_name
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header('Location: list.php');
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM ticket_contacts WHERE ticket_id = ? ORDER BY id ASC");
    $stmt->execute([$ticket_id]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT id, file_name, mime_type, created_at FROM attachments WHERE ticket_id = ? ORDER BY created_at ASC");
    $stmt->execute([$ticket_id]);
    $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT t.*, u.full_name as user_name
        FROM ticket_timeline t
        JOIN users u ON t.user_id = u.id
        WHERE t.ticket_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$ticket_id]);
    $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Erro ao carregar dados do chamado';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamado #<?php echo $ticket_id; ?> - Sistema de Chamados (Admin)</title>
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

        h5 {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .bg-light {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .attachment-item {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background-color: #fff;
            border: 1px solid #e3e6f0;
        }

        .btn-primary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-outline-primary, .btn-outline-danger {
            border-radius: 20px;
            padding: 0.3rem 1rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-text {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .list-group-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .timeline {
            position: relative;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-content {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .status-select {
            width: auto;
            display: inline-block;
            vertical-align: middle;
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

            h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <!-- Detalhes do Chamado -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h2 class="card-title">
                                            <i class="fas fa-ticket-alt me-2"></i>Chamado #<?php echo $ticket_id; ?>
                                        </h2>
                                        <small class="text-muted">
                                            Aberto por <?php echo htmlspecialchars($ticket['user_name']); ?>
                                            em <?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <select id="statusSelect" class="form-select status-select" data-ticket-id="<?php echo $ticket_id; ?>">
                                            <option value="aberto" <?php echo $ticket['status'] === 'aberto' ? 'selected' : ''; ?>>Aberto</option>
                                            <option value="em_andamento" <?php echo $ticket['status'] === 'em_andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                            <option value="fechado" <?php echo $ticket['status'] === 'fechado' ? 'selected' : ''; ?>>Fechado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-tag me-2"></i>Tipo de Incidente</h5>
                                    <p class="bg-light p-3">
                                        <?php
                                        $types = [
                                            'hardware' => ['icon' => 'desktop', 'label' => 'Hardware'],
                                            'software' => ['icon' => 'laptop-code', 'label' => 'Software'],
                                            'network' => ['icon' => 'network-wired', 'label' => 'Rede'],
                                            'printer' => ['icon' => 'print', 'label' => 'Impressora'],
                                            'email' => ['icon' => 'envelope', 'label' => 'E-mail'],
                                            'access' => ['icon' => 'key', 'label' => 'Acesso'],
                                            'other' => ['icon' => 'question-circle', 'label' => 'Outro']
                                        ];
                                        $type = $types[$ticket['incident_type']] ?? ['icon' => 'question-circle', 'label' => 'Desconhecido'];
                                        echo '<i class="fas fa-' . $type['icon'] . ' me-2"></i>' . $type['label'];
                                        ?>
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-info-circle me-2"></i>Descrição</h5>
                                    <div class="bg-light p-3"><?php echo $ticket['description']; ?></div>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-user-friends me-2"></i>Contatos</h5>
                                    <?php if (!empty($contacts)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($contacts as $contact): ?>
                                                <li class="list-group-item bg-light p-3">
                                                    <div class="d-flex flex-column">
                                                        <div>
                                                            <i class="fas fa-user me-2"></i>
                                                            <strong><?php echo htmlspecialchars($contact['contact_name'] ?? ''); ?></strong>
                                                            <span class="text-muted ms-2"><?php echo htmlspecialchars($contact['contact_phone'] ?? ''); ?></span>
                                                        </div>
                                                        <?php if (!empty($contact['observation'])): ?>
                                                            <small class="text-muted mt-1">
                                                                <i class="fas fa-comment me-1"></i><?php echo htmlspecialchars($contact['observation']); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Nenhum contato disponível.</div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-paperclip me-2"></i>Anexos</h5>
                                    <?php if (!empty($attachments)): ?>
                                        <?php foreach ($attachments as $attachment): ?>
                                            <div class="attachment-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <?php
                                                        $icon = 'file';
                                                        if (strpos($attachment['mime_type'], 'image') !== false) $icon = 'image';
                                                        elseif (strpos($attachment['mime_type'], 'pdf') !== false) $icon = 'file-pdf';
                                                        elseif (strpos($attachment['mime_type'], 'word') !== false || strpos($attachment['mime_type'], 'document') !== false) $icon = 'file-word';
                                                        elseif (strpos($attachment['mime_type'], 'text') !== false) $icon = 'file-alt';
                                                        ?>
                                                        <i class="fas fa-<?php echo $icon; ?> fa-lg text-primary me-3"></i>
                                                        <div>
                                                            <div><?php echo htmlspecialchars($attachment['file_name']); ?></div>
                                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($attachment['created_at'])); ?></small>
                                                        </div>
                                                    </div>
                                                    <a href="download.php?id=<?php echo $attachment['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Nenhum anexo disponível.</div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <h5><i class="fas fa-plus-circle me-2"></i>Adicionar Atualização</h5>
                                    <form id="updateForm" class="bg-light p-3 rounded">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                                        <div class="mb-3">
                                            <textarea id="update_description" name="description" class="form-control" rows="3" required></textarea>
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
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Adicionar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="fas fa-history me-2"></i>Timeline</h5>
                                <div class="timeline">
                                    <?php foreach ($timeline as $entry): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($entry['user_name']); ?></strong>
                                                        <small class="text-muted d-block"><?php echo date('d/m/Y H:i', strtotime($entry['created_at'])); ?></small>
                                                    </div>
                                                    <?php if ($entry['action'] === 'created'): ?>
                                                        <span class="badge bg-success">Novo</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($entry['action'] === 'created'): ?>
                                                    <p class="text-success"><i class="fas fa-ticket-alt me-2"></i>Abriu o chamado</p>
                                                <?php else: ?>
                                                    <div><?php echo $entry['description']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#update_description').summernote({
                height: 100,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol']]
                ],
                placeholder: 'Digite sua atualização...'
            });

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

            $('#updateForm').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processando...').prop('disabled', true);

                const formData = new FormData(this);
                formData.append('description', $('#update_description').summernote('code'));

                $.ajax({
                    url: '../../includes/tickets/update_ticket.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) location.reload();
                        else alert(response.message);
                        submitBtn.html('<i class="fas fa-save me-2"></i>Adicionar').prop('disabled', false);
                    },
                    error: function() {
                        alert('Erro ao processar a requisição.');
                        submitBtn.html('<i class="fas fa-save me-2"></i>Adicionar').prop('disabled', false);
                    }
                });
            });

            $('#statusSelect').on('change', function() {
                const newStatus = $(this).val();
                const ticketId = $(this).data('ticket-id');
                if (confirm(`Tem certeza que deseja alterar o status deste chamado para "${newStatus}"?`)) {
                    const select = $(this);
                    select.prop('disabled', true);

                    $.ajax({
                        url: '../../includes/tickets/update_status.php',
                        type: 'POST',
                        data: {
                            ticket_id: ticketId,
                            status: newStatus,
                            csrf_token: '<?php echo generateCSRFToken(); ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.message);
                                select.prop('disabled', false);
                            }
                        },
                        error: function() {
                            alert('Erro ao atualizar o status.');
                            select.prop('disabled', false);
                        }
                    });
                } else {
                    // Revert to original status if canceled
                    select.val('<?php echo $ticket['status']; ?>');
                }
            });
        });
    </script>
</body>

</html>