<?php
require_once __DIR__ . '/../../config/db.php';

class AuthService {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register(string $name, string $email, string $password): array {
        // 1. Verifica se e-mail já existe
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Este e-mail já está cadastrado.'];
        }

        // 2. Cria o Hash da senha (Segurança Máxima)
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 3. Cria um slug único para a bio (ex: maria-silva)
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . rand(100,999);

        // 4. Insere no banco
        $sql = "INSERT INTO users (name, email, password_hash, bio_slug) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute([$name, $email, $hash, $slug]);
            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar conta: ' . $e->getMessage()];
        }
    }

    public function login(string $email, string $password): array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifica se usuário existe E se a senha bate com o hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // Sucesso: Cria a sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_slug'] = $user['bio_slug'];
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'E-mail ou senha incorretos.'];
    }

    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}