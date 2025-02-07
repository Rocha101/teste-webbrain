<?php
require_once '../../config/config.php';
require_once '../../config/database.php';

session_start();

// Verificar autenticação
if (!isAuthenticated()) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado');
}

// Validar ID do anexo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('ID do anexo inválido');
}

$attachment_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Buscar anexo e verificar permissão
    $stmt = $db->prepare("
        SELECT a.* 
        FROM attachments a
        JOIN tickets t ON a.ticket_id = t.id
        WHERE a.id = ? AND t.user_id = ?
    ");
    $stmt->execute([$attachment_id, $user_id]);
    $attachment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attachment) {
        header('HTTP/1.1 404 Not Found');
        exit('Anexo não encontrado');
    }

    // Decodificar conteúdo do arquivo
    $file_content = base64_decode($attachment['file_content']);

    // Definir headers para download
    header('Content-Type: ' . $attachment['mime_type']);
    header('Content-Disposition: attachment; filename="' . $attachment['file_name'] . '"');
    header('Content-Length: ' . strlen($file_content));
    header('Cache-Control: private, no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Enviar conteúdo do arquivo
    echo $file_content;
    exit;

} catch (Exception $e) {
    error_log($e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit('Erro ao processar download');
}
?>
