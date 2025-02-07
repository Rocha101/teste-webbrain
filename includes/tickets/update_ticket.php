<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();

// Verificar autenticação
if (!isAuthenticated()) {
    jsonResponse(false, 'Usuário não autenticado');
}

// Validar requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método não permitido');
}

// Validar CSRF token
validateCSRF();

// Validar campos obrigatórios
if (empty($_POST['ticket_id']) || empty($_POST['description'])) {
    jsonResponse(false, 'Campos obrigatórios não preenchidos');
}

$ticket_id = (int)$_POST['ticket_id'];
$description = $_POST['description']; // Não sanitizar HTML do editor
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Verificar se o usuário tem acesso ao chamado
    $stmt = $db->prepare("SELECT id FROM tickets WHERE id = ? AND user_id = ?");
    $stmt->execute([$ticket_id, $user_id]);
    if (!$stmt->fetch()) {
        jsonResponse(false, 'Chamado não encontrado');
    }

    // Iniciar transação
    $db->beginTransaction();

    // Registrar atualização na timeline
    $stmt = $db->prepare("
        INSERT INTO ticket_timeline (ticket_id, user_id, action, description)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $ticket_id,
        $user_id,
        'update',
        $description
    ]);

    // Processar anexos adicionais
    if (!empty($_FILES['attachments']['name'][0])) {
        $stmt = $db->prepare("
            INSERT INTO attachments (ticket_id, file_name, file_content, mime_type)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($_FILES['attachments']['name'] as $index => $filename) {
            if ($_FILES['attachments']['error'][$index] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['attachments']['tmp_name'][$index];
                $mime_type = $_FILES['attachments']['type'][$index];
                
                // Validar tipo de arquivo
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                                'text/plain'];
                
                if (!in_array($mime_type, $allowed_types)) {
                    continue;
                }

                // Ler arquivo e converter para base64
                $file_content = base64_encode(file_get_contents($tmp_name));
                
                $stmt->execute([
                    $ticket_id,
                    sanitize($filename),
                    $file_content,
                    $mime_type
                ]);
            }
        }
    }

    // Confirmar transação
    $db->commit();

    jsonResponse(true, 'Atualização registrada com sucesso!');

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $db->rollBack();
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao registrar atualização. Tente novamente mais tarde.');
}
?>
