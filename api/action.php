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
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $action = $input['action'] ?? 'state';

    // Carrega estado da sessão ou do banco
    $state = $_SESSION['game_state'] ?? null;
    if (!$state) {
        $state = Auth::loadSave($userId);
    }
    $state = $engine->startIfNeeded($state);

    if ($action === 'new') {
        $state = $engine->initialState();
    } elseif ($action === 'advance') {
        $engine->advance($state);
    } elseif ($action === 'choose') {
        $engine->choose($state, (int)($input['index'] ?? -1));
    } elseif ($action === 'roll') {
        $engine->roll($state);
    } elseif ($action === 'save') {
        // apenas persiste
    } elseif ($action !== 'state') {
        throw new RuntimeException('Ação desconhecida.');
    }

    $_SESSION['game_state'] = $state;

    // Sempre grava no banco vinculado ao usuário
    Auth::saveGame($userId, $state);

    echo json_encode([
        'ok' => true,
        'data' => $engine->publicState($state),
        'user' => Auth::username()
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
