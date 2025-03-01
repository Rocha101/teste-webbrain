<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

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

        /* Card e Tabela */
        .ticket-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #fff;
            border-bottom: 2px solid var(--primary);
            padding: 1rem;
            font-weight: 500;
            color: var(--dark);
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #e3e6f0;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 20px;
            font-weight: 500;
            color: #e3e6f0;
        }

        .badge-info {
            background-color: var(--primary);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 12px;
        }

        .btn-outline-primary {
            border-radius: 20px;
            padding: 0.3rem 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary);
        }

        .empty-state h4 {
            font-weight: 500;
            color: var(--dark);
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }

            .navbar {
                padding: 1rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            .table-responsive {
                border-radius: 12px;
                overflow-x: auto;
            }

            .btn-outline-primary {
                padding: 0.2rem 0.8rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary fw-bold fs-4">
                            <i class="fas fa-ticket-alt me-2"></i>Meus Chamados
                        </h2>
                        <a href="new.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Novo Chamado
                        </a>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php else: ?>
                        <?php if (empty($tickets)): ?>
                            <div class="ticket-card">
                                <div class="empty-state">
                                    <i class="fas fa-ticket-alt"></i>
                                    <h4>Nenhum chamado encontrado</h4>
                                    <p class="text-muted">Clique em "Novo Chamado" para começar.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="ticket-card">
                                <div class="table-responsive">
                                    <table class="table">
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
                                                            'aberto' => ['class' => 'bg-success', 'icon' => 'fas fa-folder-open'],
                                                            'em_andamento' => ['class' => 'bg-warning text-dark', 'icon' => 'fas fa-clock'],
                                                            'fechado' => ['class' => 'bg-secondary', 'icon' => 'fas fa-folder']
                                                        ];
                                                        $status_labels = [
                                                            'aberto' => 'Aberto',
                                                            'em_andamento' => 'Em Andamento',
                                                            'fechado' => 'Fechado'
                                                        ];
                                                        $status = $status_badges[$ticket['status']];
                                                        ?>
                                                        <span class="status-badge <?php echo $status['class']; ?>">
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
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-paperclip me-1"></i>
                                                                <?php echo $ticket['attachment_count']; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($ticket['timeline_count'] > 0): ?>
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-history me-1"></i>
                                                                <?php echo $ticket['timeline_count']; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="view.php?id=<?php echo $ticket['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> Ver
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

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>