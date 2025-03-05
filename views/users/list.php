<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

// Restrict access to admins only
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: /path/to/tickets.php"); // Redirect non-admins to tickets page
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Query to fetch all users
    $stmt = $db->prepare("
        SELECT id, email, admin, email_verified 
        FROM users 
        ORDER BY id ASC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Erro ao carregar usuários';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários - Sistema de Chamados</title>
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

        .user-card {
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
            color: #fff;
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
                            <i class="fas fa-users me-2"></i>Lista de Usuários
                        </h2>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php else: ?>
                        <?php if (empty($users)): ?>
                            <div class="user-card">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h4>Nenhum usuário encontrado</h4>
                                    <p class="text-muted">Não há usuários cadastrados no sistema.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="user-card">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="ps-4"><i class="fas fa-hashtag me-2"></i>ID</th>
                                                <th><i class="fas fa-envelope me-2"></i>E-mail</th>
                                                <th><i class="fas fa-user-shield me-2"></i>Tipo</th>
                                                <th><i class="fas fa-check-circle me-2"></i>Verificado</th>
                                                <th class="text-center"><i class="fas fa-cogs me-2"></i>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td class="ps-4">#<?php echo $user['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td>
                                                        <span class="status-badge <?php echo $user['admin'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <i class="fas fa-user<?php echo $user['admin'] ? '-shield' : ''; ?> me-1"></i>
                                                            <?php echo $user['admin'] ? 'Admin' : 'Usuário'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge <?php echo $user['email_verified'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                            <i class="fas fa-<?php echo $user['email_verified'] ? 'check' : 'times'; ?>-circle me-1"></i>
                                                            <?php echo $user['email_verified'] ? 'Sim' : 'Não'; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="view.php?id=<?php echo $user['id']; ?>" 
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