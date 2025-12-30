<?php
// src/layout/auth_guard.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se existe o ID do usuário na sessão
if (!isset($_SESSION['user_id'])) {
    // Se não tiver, manda para o login com aviso
    header('Location: login.php?error=' . urlencode('Você precisa estar logada para acessar essa página.'));
    exit; // Mata o script aqui para ninguém ver nada do dashboard
}

// Se chegou aqui, está logada!
// Disponibiliza as variáveis para a página usar
$currentUserId = $_SESSION['user_id'];
$currentUserName = $_SESSION['user_name'] ?? 'Usuária';
$currentUserSlug = $_SESSION['user_slug'] ?? '';
?>