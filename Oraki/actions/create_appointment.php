<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    // Dados do formulário
    $clientId = $_POST['client_id'];
    $serviceId = $_POST['service_id'];
    $startTime = $_POST['start_time']; // Vem no formato YYYY-MM-DDTHH:MM
    $price = !empty($_POST['price']) ? $_POST['price'] : 0;
    $paymentMethod = $_POST['payment_method']; // Se tivermos coluna para isso
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);

    // Calcula Horário de Término (Padrão: +1 hora)
    // Futuramente podemos pegar a duração real do serviço no banco
    $startDateTime = new DateTime($startTime);
    $endDateTime = clone $startDateTime;
    $endDateTime->modify('+1 hour');
    $endTime = $endDateTime->format('Y-m-d H:i:s');

    try {
        $pdo = Database::getConnection();

        // Query de Inserção
        $sql = "INSERT INTO appointments 
                (user_id, client_id, service_id, start_time, end_time, status, notes) 
                VALUES 
                (:uid, :cid, :sid, :start, :end, :status, :notes)";

        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':uid'    => $userId,
            ':cid'    => $clientId,
            ':sid'    => $serviceId,
            ':start'  => $startTime,
            ':end'    => $endTime,
            ':status' => $status,
            ':notes'  => $notes
        ]);

        header('Location: ../dashboard.php?success=agendamento_criado');

    } catch (PDOException $e) {
        // Debug rápido: se der erro, mostra na URL (apenas dev)
        header('Location: ../dashboard.php?error=' . urlencode($e->getMessage()));
    }
}