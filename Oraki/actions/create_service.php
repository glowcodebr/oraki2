<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $price = str_replace(',', '.', $_POST['price']); // Garante formato decimal (10.50)
    $description = trim($_POST['description']);

    if (empty($name) || empty($price)) {
        header('Location: ../dashboard.php?error=dados_incompletos');
        exit;
    }

    try {
        $pdo = Database::getConnection();
        
        $sql = "INSERT INTO services (user_id, name, price, active) VALUES (:uid, :name, :price, 1)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':uid'   => $userId,
            ':name'  => $name,
            ':price' => $price
        ]);

        header('Location: ../dashboard.php?success=servico_criado');

    } catch (PDOException $e) {
        header('Location: ../dashboard.php?error=erro_banco');
    }
}