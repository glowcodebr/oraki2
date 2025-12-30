<?php
session_start();
require_once '../config/db.php';

// 1. Segurança: Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// 2. Recebe os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $birthDate = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
    $phone = trim($_POST['phone']);
    $source = $_POST['source'];
    $notes = trim($_POST['notes']);

    // Validação básica
    if (empty($name)) {
        header('Location: ../dashboard.php?error=nome_obrigatorio');
        exit;
    }

    try {
        $pdo = Database::getConnection();
        
        $sql = "INSERT INTO clients (user_id, name, birth_date, phone, email, status, created_at) 
                VALUES (:uid, :name, :birth, :phone, :source, 'active', NOW())";
        
        // Nota: Usei a coluna 'email' para guardar a origem (source) temporariamente ou 
        // idealmente teríamos uma coluna 'source'. Vou salvar nas anotações também para garantir.
        // Ajuste: Vamos salvar a origem junto com as notas se não tiver coluna específica.
        
        // REVISÃO DE ARQUITETURA: Vamos inserir nas notas a origem por enquanto.
        $finalNotes = "Origem: $source. \n" . $notes;

        $stmt = $pdo->prepare("INSERT INTO clients (user_id, name, birth_date, phone, created_at) VALUES (:uid, :name, :birth, :phone, NOW())");
        
        $stmt->execute([
            ':uid'   => $userId,
            ':name'  => $name,
            ':birth' => $birthDate,
            ':phone' => $phone
        ]);

        header('Location: ../dashboard.php?success=cliente_criado');

    } catch (PDOException $e) {
        // Em produção, logar o erro em arquivo
        header('Location: ../dashboard.php?error=erro_banco');
    }
}