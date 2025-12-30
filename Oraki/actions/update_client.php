<?php
// actions/update_client.php
session_start();
require_once '../config/db.php';

// Verifica se tem sessão e se é POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];
// Pega o ID (IMPORTANTE: O ID VEM DO INPUT HIDDEN DO MODAL)
$clientId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim($_POST['name']);
$birthDate = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
$phone = trim($_POST['phone']);
$source = $_POST['source'];
$notes = trim($_POST['notes']);
$status = isset($_POST['status']) ? $_POST['status'] : 'active';

// Debug: Se nada funcionar, descomente as 3 linhas abaixo para ver o que está chegando
// var_dump($_POST);
// echo "ID recebido: " . $clientId;
// die();

if (!$clientId || empty($name)) {
    // Se cair aqui, é porque o ID não chegou no backend
    header('Location: ../clients_list.php?error=id_ausente');
    exit;
}

try {
    $pdo = Database::getConnection();
    
    // Query de Atualização
    // Nota: Se você rodou o SQL opcional de 'source', mude 'email = :source' para 'source = :source'
    $sql = "UPDATE clients 
            SET name = :name, 
                birth_date = :birth, 
                phone = :phone, 
                email = :source, 
                notes = :notes,
                status = :status 
            WHERE id = :id AND user_id = :uid";
            
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        ':name'  => $name,
        ':birth' => $birthDate,
        ':phone' => $phone,
        ':source'=> $source, 
        ':notes' => $notes,
        ':status'=> $status,
        ':id'    => $clientId,
        ':uid'   => $userId
    ]);

    if ($result) {
        header('Location: ../clients_list.php?success=cliente_atualizado');
    } else {
        header('Location: ../clients_list.php?error=falha_sql');
    }

} catch (PDOException $e) {
    // Mostra o erro real na URL para sabermos o que é (só em desenvolvimento)
    header('Location: ../clients_list.php?error=' . urlencode($e->getMessage()));
}