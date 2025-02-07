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
if (empty($_POST['incident_type']) || empty($_POST['description'])) {
    jsonResponse(false, 'Tipo de incidente e descrição são obrigatórios');
}

// Validar contatos
if (empty($_POST['contact_names']) || empty($_POST['contact_phones'])) {
    jsonResponse(false, 'Pelo menos um contato é obrigatório');
}

// Sanitizar dados
$incident_type = sanitize($_POST['incident_type']);
$description = $_POST['description']; // Não sanitizar HTML do editor
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Iniciar transação
    $db->beginTransaction();

    // Inserir chamado
    $stmt = $db->prepare("
        INSERT INTO tickets (user_id, description, incident_type)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $description, $incident_type]);
    $ticket_id = $db->lastInsertId();

    // Inserir contatos
    $stmt = $db->prepare("
        INSERT INTO ticket_contacts (ticket_id, contact_name, contact_phone, observation)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_POST['contact_names'] as $index => $name) {
        $phone = $_POST['contact_phones'][$index];
        $note = isset($_POST['contact_notes'][$index]) ? $_POST['contact_notes'][$index] : '';
        
        if (!empty($name) && !empty($phone)) {
            $stmt->execute([
                $ticket_id,
                sanitize($name),
                sanitize($phone),
                sanitize($note)
            ]);
        }
    }

    // Processar anexos
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

    // Registrar na timeline
    $stmt = $db->prepare("
        INSERT INTO ticket_timeline (ticket_id, user_id, action, description)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $ticket_id,
        $user_id,
        'created',
        'Chamado aberto'
    ]);

    // Confirmar transação
    $db->commit();

    jsonResponse(true, 'Chamado criado com sucesso!', ['ticket_id' => $ticket_id]);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $db->rollBack();
    error_log($e->getMessage());
    jsonResponse(false, 'Erro ao criar chamado. Tente novamente mais tarde.');
}
?>
