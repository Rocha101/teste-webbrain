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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../components/navbar.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Meus Chamados</h2>
            <a href="new.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Chamado
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php else: ?>
            <?php if (empty($tickets)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4>Nenhum chamado encontrado</h4>
                        <p class="text-muted">Clique no botão "Novo Chamado" para criar seu primeiro chamado.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Anexos</th>
                                    <th>Atualizações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td><?php echo $ticket['id']; ?></td>
                                        <td>
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
                                        </td>
                                        <td>
                                            <?php
                                            $status_badges = [
                                                'aberto' => 'badge bg-success',
                                                'em_andamento' => 'badge bg-warning text-dark',
                                                'fechado' => 'badge bg-secondary'
                                            ];
                                            $status_labels = [
                                                'aberto' => 'Aberto',
                                                'em_andamento' => 'Em Andamento',
                                                'fechado' => 'Fechado'
                                            ];
                                            ?>
                                            <span class="<?php echo $status_badges[$ticket['status']]; ?>">
                                                <?php echo $status_labels[$ticket['status']]; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                                        <td>
                                            <?php if ($ticket['attachment_count'] > 0): ?>
                                                <span class="badge bg-info">
                                                    <?php echo $ticket['attachment_count']; ?> anexo(s)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ticket['timeline_count'] > 0): ?>
                                                <span class="badge bg-info">
                                                    <?php echo $ticket['timeline_count']; ?> atualização(ões)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Ver
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
