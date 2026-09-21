<?php
require_once __DIR__.'/../config.php';

class Auth {
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function userId(): ?int {
        self::startSession();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function username(): ?string {
        self::startSession();
        return $_SESSION['username'] ?? null;
    }

    public static function check(): bool {
        return self::userId() !== null;
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: login.php');
            exit;
        }
    }

    public static function login(string $username, string $password): array {
        $stmt = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['ok' => false, 'error' => 'Usuário ou senha inválidos.'];
        }
        self::startSession();
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        // limpa estado de jogo antigo da sessão
        unset($_SESSION['game_state']);
        return ['ok' => true, 'username' => $user['username']];
    }

    public static function register(string $username, string $password): array {
        $username = trim($username);
        if (strlen($username) < 3 || strlen($username) > 50) {
            return ['ok' => false, 'error' => 'Usuário deve ter entre 3 e 50 caracteres.'];
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['ok' => false, 'error' => 'Use apenas letras, números e underscore.'];
        }
        if (strlen($password) < 4) {
            return ['ok' => false, 'error' => 'Senha deve ter pelo menos 4 caracteres.'];
        }

        $stmt = db()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'error' => 'Este nome de usuário já está em uso.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
        $ins->execute([$username, $hash]);

        return self::login($username, $password);
    }

    public static function logout(): void {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function loadSave(int $userId): ?array {
        $stmt = db()->prepare('SELECT state_json FROM saves WHERE user_id = ? AND slot_name = ? LIMIT 1');
        $stmt->execute([$userId, 'Story']);
        $row = $stmt->fetch();
        if (!$row) return null;
        $state = json_decode($row['state_json'], true);
        return is_array($state) ? $state : null;
    }

    public static function saveGame(int $userId, array $state): void {
        $json = json_encode($state, JSON_UNESCAPED_UNICODE);
        $stmt = db()->prepare(
            'INSERT INTO saves (user_id, slot_name, state_json, updated_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE state_json = VALUES(state_json), updated_at = NOW()'
        );
        $stmt->execute([$userId, 'Story', $json]);
    }
}
