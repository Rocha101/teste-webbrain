<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$ticket_id = (int)$_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Buscar dados do chamado
    $stmt = $db->prepare("
        SELECT t.*, u.full_name as user_name
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ? AND t.user_id = ?
    ");
    $stmt->execute([$ticket_id, $_SESSION['user_id']]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header('Location: list.php');
        exit;
    }

    // Buscar contatos
    $stmt = $db->prepare("
        SELECT * FROM ticket_contacts
        WHERE ticket_id = ?
        ORDER BY id ASC
    ");
    $stmt->execute([$ticket_id]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar anexos
    $stmt = $db->prepare("
        SELECT id, file_name, mime_type, created_at
        FROM attachments
        WHERE ticket_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$ticket_id]);
    $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar timeline
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
    <title>Visualizar Chamado #<?php echo $ticket_id; ?> - Sistema de Chamados</title>
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
            background-color: #f8f9fa;
        }

        .content-wrapper {
            flex: 1 0 auto;
            padding-bottom: 2rem;
        }

        footer {
            flex-shrink: 0;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }

        .attachment-item {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .attachment-item:hover {
            background-color: #f8f9fa;
        }

        .attachment-item .btn-outline-primary {
            opacity: 0.7;
            transition: all 0.2s ease-in-out;
        }

        .attachment-item:hover .btn-outline-primary {
            opacity: 1;
        }

        .list-group {
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="content-wrapper">
        <?php include '../components/navbar.php'; ?>

        <div class="container my-5">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Detalhes do Chamado -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h2 class="card-title mb-0">
                                            <i class="fas fa-ticket-alt text-primary me-2"></i>
                                            Chamado #<?php echo $ticket_id; ?>
                                        </h2>
                                        <small class="text-muted">
                                            Aberto por <?php echo htmlspecialchars($ticket['user_name']); ?>
                                            em <?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php echo $ticket['status'] === 'aberto' ? 'success' : ($ticket['status'] === 'em_andamento' ? 'warning' : 'secondary'); ?> p-2">
                                        <i class="fas fa-<?php echo $ticket['status'] === 'aberto' ? 'door-open' : ($ticket['status'] === 'em_andamento' ? 'sync' : 'door-closed'); ?> me-1"></i>
                                        <?php
                                        echo $ticket['status'] === 'aberto' ? 'Aberto' : ($ticket['status'] === 'em_andamento' ? 'Em Andamento' : 'Fechado');
                                        ?>
                                    </span>
                                </div>

                                <!-- [Rest of the content sections with improved styling] -->
                                <div class="mb-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-tag me-2"></i>Tipo de Incidente
                                    </h5>
                                    <p class="bg-light p-3 rounded">
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

                                <div>
                                    <h5 class="text-primary">
                                        <i class="fas fa-info-circle me-2"></i>Descrição
                                    </h5>
                                    <div class="bg-light p-3 rounded">
                                        <?php echo $ticket['description']; ?>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-user-friends me-2"></i>Contatos
                                    </h5>
                                    <?php if (!empty($contacts)): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($contacts as $contact): ?>
                                                <li class="list-group-item">
                                                    <div class="d-flex flex-column flex-md-row align-items-md-center">
                                                        <i class="fas fa-user me-2"></i>
                                                        <div class="flex-grow-1">
                                                            <strong><?php echo htmlspecialchars($contact['contact_name'] ?? ''); ?></strong>
                                                            <span class="text-muted ms-md-2">
                                                                <?php echo htmlspecialchars($contact['contact_phone'] ?? ''); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Observation -->
                                                    <?php if (!empty($contact['observation'])): ?>
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-comment me-1"></i>
                                                                <?php echo htmlspecialchars($contact['observation']); ?>
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Nenhum contato disponível para este chamado.
                                        </div>
                                    <?php endif; ?>
                                </div>


                                <!-- Anexos -->
                                <div class="mb-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-paperclip me-2"></i>Anexos
                                    </h5>
                                    <?php if (!empty($attachments)): ?>
                                        <div class="list-group">
                                            <?php foreach ($attachments as $attachment): ?>
                                                <div class="attachment-item list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                                    <div class="d-flex align-items-center">
                                                        <?php
                                                        // Define icons based on mime type
                                                        $icon = 'file';
                                                        if (strpos($attachment['mime_type'], 'image') !== false) {
                                                            $icon = 'image';
                                                        } elseif (strpos($attachment['mime_type'], 'pdf') !== false) {
                                                            $icon = 'file-pdf';
                                                        } elseif (
                                                            strpos($attachment['mime_type'], 'word') !== false ||
                                                            strpos($attachment['mime_type'], 'document') !== false
                                                        ) {
                                                            $icon = 'file-word';
                                                        } elseif (strpos($attachment['mime_type'], 'text') !== false) {
                                                            $icon = 'file-alt';
                                                        }
                                                        ?>
                                                        <i class="fas fa-<?php echo $icon; ?> fa-lg text-primary me-3"></i>
                                                        <div>
                                                            <div class="fw-semibold"><?php echo htmlspecialchars($attachment['file_name']); ?></div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?php echo date('d/m/Y H:i', strtotime($attachment['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <a href="download.php?id=<?php echo $attachment['id']; ?>"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Baixar arquivo">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Nenhum anexo disponível para este chamado.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Formulário para nova atualização -->
                                <div class="mb-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Adicionar Atualização
                                    </h5>
                                    <form id="updateForm" class="bg-light p-4 rounded">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">

                                        <div class="mb-3">
                                            <textarea id="update_description" name="description"
                                                class="form-control" rows="3" required></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-paperclip me-2"></i>Anexos Adicionais
                                            </label>
                                            <input type="file" class="form-control" name="attachments[]"
                                                multiple accept="image/*,.pdf,.doc,.docx,.txt">
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Adicionar Atualização
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline with improved styling -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <h5 class="card-title text-primary mb-4">
                                    <i class="fas fa-history me-2"></i>Timeline
                                </h5>
                                <div class="timeline">
                                    <?php foreach ($timeline as $entry): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-header mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="user-info">
                                                            <strong class="d-block">
                                                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                                                <?php echo htmlspecialchars($entry['user_name']); ?>
                                                            </strong>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?php echo date('d/m/Y H:i', strtotime($entry['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <?php if ($entry['action'] === 'created'): ?>
                                                            <span class="badge bg-success-subtle text-success">
                                                                <i class="fas fa-plus-circle me-1"></i>Novo
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="timeline-body">
                                                    <?php if ($entry['action'] === 'created'): ?>
                                                        <p class="mb-0 text-success">
                                                            <i class="fas fa-ticket-alt me-2"></i>Abriu o chamado
                                                        </p>
                                                    <?php else: ?>
                                                        <div class="bg-light rounded p-3">
                                                            <?php echo $entry['description']; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
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

    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="far fa-copyright me-1"></i> <?php echo date('Y'); ?>
                Sistema de Chamados TI - Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <!-- [Scripts section remains largely the same, with added loading states] -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#update_description').summernote({
                height: 100,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']],
                ],
                placeholder: 'Digite sua atualização aqui...'
            });

            $('#updateForm').on('submit', function(e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processando...');
                submitBtn.prop('disabled', true);

                const formData = new FormData(this);
                formData.append('description', $('#update_description').summernote('code'));

                $.ajax({
                    url: '../../includes/tickets/update_ticket.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a requisição. Tente novamente.');
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>