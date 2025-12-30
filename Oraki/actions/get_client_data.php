<?php
// actions/get_client_data.php
session_start();
require_once '../src/services/ClientService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

try {
    $service = new ClientService();
    $data = $service->getClientFullProfile($_SESSION['user_id'], (int)$_GET['id']);
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro interno']);
}