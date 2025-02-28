<?php
// Configurações gerais
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Sao_Paulo');

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Funções de utilidade
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function isAdult($birthDate) {
    $today = new DateTime();
    $diff = $today->diff(new DateTime($birthDate));
    return $diff->y >= 18;
}

function generateVerificationCode() {
    return sprintf("%06d", mt_rand(0, 999999));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^\(\d{2}\)\s\d{4,5}-\d{4}$/', $phone);
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotAuthenticated() {
    if (!isAuthenticated()) {
        header('Location: teste-webbrain/views/auth/login.php');
        exit;
    }
}

function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        header('Location: teste-webbrain/index.php');
        exit;
    }
}

// Função para criar resposta JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Função para validar token CSRF
function validateCSRF() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        jsonResponse(false, 'Token de segurança inválido');
    }
}

// Gerar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

?>
