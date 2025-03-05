<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
redirectIfNotAuthenticated();

// Restrict access to admins only
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /path/to/tickets.php'); // Adjust redirect path
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php'); // Redirect to user list if ID is invalid
    exit;
}

$user_id = (int)$_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("
        SELECT id, full_name, birth_date, email, phone, whatsapp, password, city, state, 
               email_verified, verification_code, admin, created_at, updated_at
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: users.php'); // Redirect if user not found
        exit;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Erro ao carregar dados do usuário';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuário #<?php echo $user_id; ?> - Sistema de Chamados</title>
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

        .btn-primary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-outline-primary {
            border-radius: 20px;
            padding: 0.3rem 1rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h2 class="card-title">
                                            <i class="fas fa-user me-2"></i>Usuário #<?php echo $user_id; ?>
                                        </h2>
                                        <small class="text-muted">
                                            Criado em <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge <?php echo $user['admin'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $user['admin'] ? 'Administrador' : 'Usuário'; ?>
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-user-circle me-2"></i>Nome Completo</h5>
                                    <p class="bg-light p-3"><?php echo htmlspecialchars($user['full_name']); ?></p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-calendar-alt me-2"></i>Data de Nascimento</h5>
                                    <p class="bg-light p-3"><?php echo date('d/m/Y', strtotime($user['birth_date'])); ?></p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-envelope me-2"></i>E-mail</h5>
                                    <p class="bg-light p-3">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                        <span class="badge <?php echo $user['email_verified'] ? 'bg-success' : 'bg-warning text-dark'; ?> ms-2">
                                            <?php echo $user['email_verified'] ? 'Verificado' : 'Não Verificado'; ?>
                                        </span>
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-phone me-2"></i>Telefone</h5>
                                    <p class="bg-light p-3"><?php echo htmlspecialchars($user['phone']); ?></p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fab fa-whatsapp me-2"></i>WhatsApp</h5>
                                    <p class="bg-light p-3"><?php echo htmlspecialchars($user['whatsapp']); ?></p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-map-marker-alt me-2"></i>Localização</h5>
                                    <p class="bg-light p-3">
                                        <?php echo htmlspecialchars($user['city']) . ' - ' . htmlspecialchars($user['state']); ?>
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <h5><i class="fas fa-clock me-2"></i>Última Atualização</h5>
                                    <p class="bg-light p-3"><?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></p>
                                </div>

                                <?php if (!empty($user['verification_code'])): ?>
                                    <div class="mb-4">
                                        <h5><i class="fas fa-key me-2"></i>Código de Verificação</h5>
                                        <p class="bg-light p-3"><?php echo htmlspecialchars($user['verification_code']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-end">
                                    <a href="list.php" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-arrow-left me-1"></i>Voltar
                                    </a>
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
</body>

</html>