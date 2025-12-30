<?php
// config/db.php

class Database {
    private static $instance = null;
    
    public static function getConnection() {
        if (self::$instance === null) {
            // Configurações (em produção, isso viria de variáveis de ambiente .env)
            $host = 'localhost';
            $db   = 'oraki';
            $user = 'root'; 
            $pass = '';     
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Segurança: Nunca mostre a senha ou detalhes técnicos do erro na tela do usuário
                die("Erro de conexão com o banco de dados. Por favor, tente novamente mais tarde.");
            }
        }
        return self::$instance;
    }
}