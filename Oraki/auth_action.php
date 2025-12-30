<?php
// auth_action.php
require_once 'src/services/AuthService.php';

$auth = new AuthService();
$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $auth->register($name, $email, $password);

    if ($result['success']) {
        // Redireciona para login com mensagem de sucesso
        header('Location: login.php?registered=1');
    } else {
        // Volta para cadastro com erro
        header('Location: register.php?error=' . urlencode($result['message']));
    }
}

if ($action === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $auth->login($email, $password);

    if ($result['success']) {
        header('Location: dashboard.php');
    } else {
        header('Location: login.php?error=' . urlencode($result['message']));
    }
}