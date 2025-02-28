<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

// Buscar chamados do usuário
try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("
        SELECT t.*, 
               (SELECT COUNT(*) FROM attachments WHERE ticket_id = t.id) as attachment_count,
               (SELECT COUNT(*) FROM ticket_timeline WHERE ticket_id = t.id) as timeline_count
        FROM tickets t
        WHERE t.user_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Erro ao carregar chamados';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados - Sistema de Chamados</title>
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

        .ticket-card {
            transition: transform 0.2s;
        }

        .status-badge {
            padding: 0.5em 1em;
            font-weight: 500;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>

<body class="bg-light">
    <div class="content-wrapper">
        <?php include '../components/navbar.php'; ?>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">
                            <i class="fas fa-ticket-alt me-2"></i>Meus Chamados
                        </h2>
                        <a href="new.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Novo Chamado
                        </a>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php else: ?>
                        <?php if (empty($tickets)): ?>
                            <div class="card shadow-lg border-0 rounded-lg ticket-card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                                    <h4>Nenhum chamado encontrado</h4>
                                    <p class="text-muted">Clique no botão "Novo Chamado" para criar seu primeiro chamado.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card shadow-lg border-0 rounded-lg ticket-card">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4"><i class="fas fa-hashtag me-2"></i>ID</th>
                                                <th><i class="fas fa-tag me-2"></i>Tipo</th>
                                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                                <th><i class="far fa-calendar-alt me-2"></i>Data</th>
                                                <th><i class="fas fa-paperclip me-2"></i>Anexos</th>
                                                <th><i class="fas fa-history me-2"></i>Atualizações</th>
                                                <th class="text-center"><i class="fas fa-cogs me-2"></i>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tickets as $ticket): ?>
                                                <tr>
                                                    <td class="ps-4">#<?php echo $ticket['id']; ?></td>
                                                    <td>
                                                        <?php
                                                        $types = [
                                                            'hardware' => ['icon' => 'fas fa-desktop', 'label' => 'Hardware'],
                                                            'software' => ['icon' => 'fas fa-laptop-code', 'label' => 'Software'],
                                                            'network' => ['icon' => 'fas fa-network-wired', 'label' => 'Rede'],
                                                            'printer' => ['icon' => 'fas fa-print', 'label' => 'Impressora'],
                                                            'email' => ['icon' => 'fas fa-envelope', 'label' => 'E-mail'],
                                                            'access' => ['icon' => 'fas fa-key', 'label' => 'Acesso'],
                                                            'other' => ['icon' => 'fas fa-question-circle', 'label' => 'Outro']
                                                        ];
                                                        $type = $types[$ticket['incident_type']] ?? ['icon' => 'fas fa-question-circle', 'label' => 'Desconhecido'];
                                                        echo '<i class="' . $type['icon'] . ' me-2"></i>' . $type['label'];
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status_badges = [
                                                            'aberto' => ['class' => 'badge bg-success', 'icon' => 'fas fa-folder-open'],
                                                            'em_andamento' => ['class' => 'badge bg-warning text-dark', 'icon' => 'fas fa-clock'],
                                                            'fechado' => ['class' => 'badge bg-secondary', 'icon' => 'fas fa-folder']
                                                        ];
                                                        $status_labels = [
                                                            'aberto' => 'Aberto',
                                                            'em_andamento' => 'Em Andamento',
                                                            'fechado' => 'Fechado'
                                                        ];
                                                        $status = $status_badges[$ticket['status']];
                                                        ?>
                                                        <span class="<?php echo $status['class']; ?> status-badge">
                                                            <i class="<?php echo $status['icon']; ?> me-1"></i>
                                                            <?php echo $status_labels[$ticket['status']]; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($ticket['attachment_count'] > 0): ?>
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-paperclip me-1"></i>
                                                                <?php echo $ticket['attachment_count']; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($ticket['timeline_count'] > 0): ?>
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-history me-1"></i>
                                                                <?php echo $ticket['timeline_count']; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="view.php?id=<?php echo $ticket['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> Ver Detalhes
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
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
</body>

</html>