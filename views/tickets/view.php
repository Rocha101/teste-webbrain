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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../components/navbar.php'; ?>

    <div class="container my-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Detalhes do Chamado -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="card-title">Chamado #<?php echo $ticket_id; ?></h2>
                                <span class="badge bg-<?php echo $ticket['status'] === 'aberto' ? 'success' : ($ticket['status'] === 'em_andamento' ? 'warning' : 'secondary'); ?>">
                                    <?php
                                    echo $ticket['status'] === 'aberto' ? 'Aberto' : 
                                         ($ticket['status'] === 'em_andamento' ? 'Em Andamento' : 'Fechado');
                                    ?>
                                </span>
                            </div>

                            <div class="mb-4">
                                <h5>Tipo de Incidente</h5>
                                <p>
                                    <?php
                                    $types = [
                                        'hardware' => 'Hardware',
                                        'software' => 'Software',
                                        'network' => 'Rede',
                                        'printer' => 'Impressora',
                                        'email' => 'E-mail',
                                        'access' => 'Acesso',
                                        'other' => 'Outro'
                                    ];
                                    echo $types[$ticket['incident_type']] ?? 'Desconhecido';
                                    ?>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5>Descrição</h5>
                                <div class="border rounded p-3 bg-white">
                                    <?php echo $ticket['description']; ?>
                                </div>
                            </div>

                            <?php if (!empty($contacts)): ?>
                                <div class="mb-4">
                                    <h5>Contatos</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Telefone</th>
                                                    <th>Observação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($contacts as $contact): ?>
                                                    <tr>
                                                        <td><?php echo $contact['contact_name']; ?></td>
                                                        <td><?php echo $contact['contact_phone']; ?></td>
                                                        <td><?php echo $contact['observation']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($attachments)): ?>
                                <div class="mb-4">
                                    <h5>Anexos</h5>
                                    <div class="list-group">
                                        <?php foreach ($attachments as $attachment): ?>
                                            <a href="download.php?id=<?php echo $attachment['id']; ?>" 
                                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-paperclip me-2"></i>
                                                    <?php echo $attachment['file_name']; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($attachment['created_at'])); ?>
                                                </small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Formulário para nova atualização -->
                            <div class="mb-4">
                                <h5>Adicionar Atualização</h5>
                                <form id="updateForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                                    
                                    <div class="mb-3">
                                        <textarea id="update_description" name="description" class="form-control" rows="3" required></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Anexos Adicionais</label>
                                        <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Adicionar Atualização
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Timeline</h5>
                            <div class="timeline">
                                <?php foreach ($timeline as $entry): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <strong><?php echo $entry['user_name']; ?></strong>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($entry['created_at'])); ?>
                                                </small>
                                            </div>
                                            <?php if ($entry['action'] === 'created'): ?>
                                                <p class="mb-0">Abriu o chamado</p>
                                            <?php else: ?>
                                                <div class="mt-2">
                                                    <?php echo $entry['description']; ?>
                                                </div>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Summernote
            $('#update_description').summernote({
                height: 100,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']],
                ]
            });

            // Submissão do formulário de atualização
            $('#updateForm').on('submit', function(e) {
                e.preventDefault();
                
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
                        }
                    },
                    error: function() {
                        alert('Erro ao processar a requisição. Tente novamente.');
                    }
                });
            });
        });
    </script>
</body>
</html>
