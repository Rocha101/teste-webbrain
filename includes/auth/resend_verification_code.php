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
if (empty($_POST['email'])) {
    jsonResponse(false, 'E-mail é obrigatório');
}

$email = sanitize($_POST['email']);

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar se o usuário existe e não está verificado
    $stmt = $db->prepare("
        SELECT id 
        FROM users 
        WHERE email = ? AND email_verified = 0
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, 'E-mail não encontrado ou já verificado');
    }

    // Gerar novo código
    $verification_code = generateVerificationCode();

    // Atualizar código no banco
    $stmt = $db->prepare("
        UPDATE users 
        SET verification_code = ? 
        WHERE id = ?
    ");
    $stmt->execute([$verification_code, $user['id']]);

    // Enviar novo e-mail (implementar depois)
    sendVerificationEmail($email, $verification_code);

    jsonResponse(true, 'Novo código de verificação enviado com sucesso!');

} catch (PDOException $e) {
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao reenviar código. Tente novamente mais tarde.');
}
?>
