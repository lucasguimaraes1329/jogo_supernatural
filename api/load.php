<?php
require_once __DIR__.'/../includes/Auth.php';
require_once __DIR__.'/../includes/GameEngine.php';

Auth::startSession();
header('Content-Type: application/json; charset=utf-8');

try {
    if (!Auth::check()) {
        throw new RuntimeException('Você precisa estar logado.');
    }

    $userId = Auth::userId();
    $engine = new GameEngine();

    // Prioridade: sessão → banco → inicial
    $state = $_SESSION['game_state'] ?? null;
    if (!$state) {
        $state = Auth::loadSave($userId);
    }
    $state = $engine->startIfNeeded($state);
    $_SESSION['game_state'] = $state;

    echo json_encode([
        'ok' => true,
        'data' => $engine->publicState($state),
        'user' => Auth::username()
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
