<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();

// Validar requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método não permitido');
}

// Validar CSRF token
validateCSRF();

// Validar campos obrigatórios
if (empty($_POST['email']) || empty($_POST['code'])) {
    jsonResponse(false, 'Todos os campos são obrigatórios');
}

$email = sanitize($_POST['email']);
$code = sanitize($_POST['code']);

// Validar formato do código
if (!preg_match('/^\d{6}$/', $code)) {
    jsonResponse(false, 'Código de verificação inválido');
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar código
    $stmt = $db->prepare("
        SELECT id, verification_code 
        FROM users 
        WHERE email = ? AND email_verified = 0
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, 'E-mail não encontrado ou já verificado');
    }

    if ($user['verification_code'] !== $code) {
        jsonResponse(false, 'Código de verificação incorreto');
    }

    // Atualizar status de verificação
    $stmt = $db->prepare("
        UPDATE users 
        SET email_verified = 1, verification_code = NULL 
        WHERE id = ?
    ");
    $stmt->execute([$user['id']]);

    // Limpar sessão de verificação
    unset($_SESSION['verification_email']);

    jsonResponse(true, 'E-mail verificado com sucesso! Você já pode fazer login.');

} catch (PDOException $e) {
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao processar verificação. Tente novamente mais tarde.');
}
?>
