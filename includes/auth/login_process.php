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
if (empty($_POST['email']) || empty($_POST['password'])) {
    jsonResponse(false, 'Todos os campos são obrigatórios');
}

$email = sanitize($_POST['email']);
$password = $_POST['password'];

// Validar formato do email
if (!validateEmail($email)) {
    jsonResponse(false, 'E-mail inválido');
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT id, password, email_verified, admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, 'E-mail ou senha incorretos');
    }

    if (!password_verify($password, $user['password'])) {
        jsonResponse(false, 'E-mail ou senha incorretos');
    }

    if (!$user['email_verified']) {
        jsonResponse(false, 'Por favor, verifique seu e-mail antes de fazer login');
    }

    // Criar sessão
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = $user['admin'];
    $_SESSION['last_activity'] = time();

    // Regenerar ID da sessão para prevenir fixação de sessão
    session_regenerate_id(true);

    jsonResponse(true, 'Login realizado com sucesso');

} catch (PDOException $e) {
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao processar login. Tente novamente mais tarde.');
}
?>
