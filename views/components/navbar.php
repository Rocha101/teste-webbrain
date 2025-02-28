<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="new.php"><i class="fas fa-plus-circle me-1"></i> Novo Chamado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list.php"><i class="fas fa-list me-1"></i> Meus Chamados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/teste-webbrain/views/auth/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Sair
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i> Cadastro</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>