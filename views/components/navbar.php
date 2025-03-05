<?php
if (!isset($_SESSION)) {
    session_start();
}

$isAdmin = $_SESSION['is_admin'] ?? false;
?>
<style>
    :root {
        --primary: #007bff;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }

    /* Navbar Styles */
    .navbar {
        background-color: var(--primary);
        padding: 1rem 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: 600;
        color: #fff;
        font-size: 1.5rem;
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-right: 1rem;
        transition: color 0.2s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #fff;
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.5);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/teste-webbrain/index.php">
            <i class="fas fa-headset me-2"></i>Sistema de Chamados TI
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id']) and !$isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/tickets/new.php"><i class="fas fa-plus-circle me-1"></i> Novo Chamado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/tickets/list.php"><i class="fas fa-list me-1"></i> Meus Chamados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/auth/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Sair
                        </a>
                    </li>

                <?php elseif (isset($_SESSION['user_id']) and $isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/tickets/list.php"><i class="fas fa-list me-1"></i> Chamados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/users/list.php"><i class="fas fa-users me-1"></i> Usuários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/auth/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Sair
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/auth/login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/auth/register.php"><i class="fas fa-user-plus me-1"></i> Cadastro</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>