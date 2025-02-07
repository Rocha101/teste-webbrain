<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../mail/Mailer.php';

session_start();

// Validar requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método não permitido');
}

// Validar CSRF token
validateCSRF();

// Validar campos obrigatórios
$required_fields = ['full_name', 'birth_date', 'email', 'phone', 'whatsapp', 'password', 'confirm_password', 'state', 'city'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        jsonResponse(false, 'Todos os campos são obrigatórios');
    }
}

// Sanitizar e validar dados
$full_name = sanitize($_POST['full_name']);
$birth_date = sanitize($_POST['birth_date']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$whatsapp = sanitize($_POST['whatsapp']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$state = sanitize($_POST['state']);
$city = sanitize($_POST['city']);

// Validar formato do email
if (!validateEmail($email)) {
    jsonResponse(false, 'E-mail inválido');
}

// Validar idade mínima
if (!validateDate($birth_date) || !isAdult($birth_date)) {
    jsonResponse(false, 'Você deve ter pelo menos 18 anos para se cadastrar');
}

// Validar formato dos telefones
if (!validatePhone($phone) || !validatePhone($whatsapp)) {
    jsonResponse(false, 'Formato de telefone inválido');
}

// Validar senha
if ($password !== $confirm_password) {
    jsonResponse(false, 'As senhas não conferem');
}

if (strlen($password) < 8) {
    jsonResponse(false, 'A senha deve ter pelo menos 8 caracteres');
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar se email já existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Este e-mail já está cadastrado');
    }

    // Gerar código de verificação
    $verification_code = generateVerificationCode();

    // Enviar e-mail de verificação
    $mailer = new Mailer();
    if (!$mailer->sendVerificationEmail($email, $verification_code)) {
        jsonResponse(false, 'Erro ao enviar e-mail de verificação. Tente novamente mais tarde.');
    }

    // Inserir usuário
    $stmt = $db->prepare("
        INSERT INTO users (full_name, birth_date, email, phone, whatsapp, password, state, city, verification_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $full_name,
        $birth_date,
        $email,
        $phone,
        $whatsapp,
        password_hash($password, PASSWORD_DEFAULT),
        $state,
        $city,
        $verification_code
    ]);

    $_SESSION['verification_email'] = $email;
    jsonResponse(true, 'Cadastro realizado com sucesso! Por favor, verifique seu e-mail.');

} catch (PDOException $e) {
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao processar cadastro. Tente novamente mais tarde.');
}
?>
