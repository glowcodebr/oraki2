<?php
// actions/manage_notes.php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// 1. Segurança
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$userId = $_SESSION['user_id'];
// Recebe o JSON enviado pelo Javascript
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    $pdo = Database::getConnection();

    // --- ADICIONAR NOVA TAREFA ---
    if ($action === 'add') {
        $content = trim($input['content']);
        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Conteúdo vazio']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO daily_notes (user_id, content, created_at, is_completed) VALUES (:uid, :content, CURDATE(), 0)");
        $stmt->execute(['uid' => $userId, 'content' => $content]);
        
        // Retorna o ID criado para o JS atualizar a tela
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }

    // --- MARCAR COMO FEITO/NÃO FEITO ---
    if ($action === 'toggle') {
        $noteId = $input['id'];
        $isChecked = $input['completed'] ? 1 : 0;

        // Garante que a nota pertence ao usuário logado (Segurança!)
        $stmt = $pdo->prepare("UPDATE daily_notes SET is_completed = :completed WHERE id = :id AND user_id = :uid");
        $stmt->execute(['completed' => $isChecked, 'id' => $noteId, 'uid' => $userId]);

        echo json_encode(['success' => true]);
        exit;
    }
    
    // --- EXCLUIR TAREFA (Bônus) ---
    if ($action === 'delete') {
         $noteId = $input['id'];
         $stmt = $pdo->prepare("DELETE FROM daily_notes WHERE id = :id AND user_id = :uid");
         $stmt->execute(['id' => $noteId, 'uid' => $userId]);
         echo json_encode(['success' => true]);
         exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor']);
}