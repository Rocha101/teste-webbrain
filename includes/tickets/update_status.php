<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();
header('Content-Type: application/json');

// Validate request method and admin status
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

// Validate CSRF token (assuming validateCSRF() is defined in config.php)
validateCSRF();

// Validate ticket ID and status
if (!isset($_POST['ticket_id']) || !is_numeric($_POST['ticket_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$ticket_id = (int)$_POST['ticket_id'];
$new_status = $_POST['status'];
$valid_statuses = ['aberto', 'em_andamento', 'fechado'];

if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status inválido']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if ticket exists
    $stmt = $db->prepare("SELECT status FROM tickets WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        echo json_encode(['success' => false, 'message' => 'Chamado não encontrado']);
        exit;
    }

    if ($ticket['status'] === $new_status) {
        echo json_encode(['success' => false, 'message' => 'O chamado já está neste status']);
        exit;
    }

    // Update ticket status
    $stmt = $db->prepare("UPDATE tickets SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $ticket_id]);

    function getNameByStatus($status) {
        switch ($status) {
            case 'aberto':
                return 'Aberto';
            case 'em_andamento':
                return 'Em Andamento';
            case 'fechado':
                return 'Fechado';
            default:
                return 'Desconhecido';
        }
    }

    // Add timeline entry with corrected status name
    $status_name = getNameByStatus($new_status);

    // Add timeline entry
    $action_description = "Status alterado para '$status_name' pelo administrador";
    $stmt = $db->prepare("
        INSERT INTO ticket_timeline (ticket_id, user_id, action, description, created_at)
        VALUES (?, ?, 'status_update', ?, NOW())
    ");
    $stmt->execute([$ticket_id, $_SESSION['user_id'], $action_description]);

    echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status']);
}
?>