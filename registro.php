<?php
require_once __DIR__.'/includes/Auth.php';
Auth::startSession();
if (Auth::check()) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if ($pass !== $pass2) {
        $error = 'As senhas não coincidem.';
    } else {
        $res = Auth::register($user, $pass);
        if ($res['ok']) {
            header('Location: index.php');
            exit;
        }
        $error = $res['error'] ?? 'Erro ao cadastrar.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cadastrar — Scooby-doo</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-form{display:flex;flex-direction:column;gap:12px;max-width:320px;margin:0 auto;text-align:left}
    .auth-form label{font-size:11px;letter-spacing:1px;color:#aab4ae}
    .auth-form input{
      padding:12px 14px;background:#151e1d;border:1px solid #58635e;color:#e9ece7;
      font:inherit;width:100%
    }
    .auth-form input:focus{outline:none;border-color:#d5c45c}
    .auth-error{color:#e0aaa0;font-size:13px;margin:8px 0;text-align:center}
    .auth-links{margin-top:18px;font-size:12px;color:#88938d}
    .auth-links a{color:#d5c45c;text-decoration:none}
    .auth-links a:hover{text-decoration:underline}
  </style>
</head>
<body class="title">
  <div class="grain"></div>
  <main class="title-card">
    <div class="eyebrow">SCOOBY-DOO</div>
    <h1 style="font-size:clamp(36px,7vw,64px)">CADASTRO</h1>
    <?php if ($error): ?><div class="auth-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
      <div>
        <label for="username">USUÁRIO</label>
        <input type="text" id="username" name="username" required autofocus minlength="3" maxlength="50"
               pattern="[a-zA-Z0-9_]+" title="Letras, números e underscore"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username">
      </div>
      <div>
        <label for="password">SENHA</label>
        <input type="password" id="password" name="password" required minlength="4" autocomplete="new-password">
      </div>
      <div>
        <label for="password2">CONFIRMAR SENHA</label>
        <input type="password" id="password2" name="password2" required minlength="4" autocomplete="new-password">
      </div>
      <button type="submit" style="margin-top:8px;padding:13px 24px;background:#1c2927;border:1px solid #7b8580;color:#fff">
        CRIAR CONTA
      </button>
    </form>
    <div class="auth-links">
      Já tem conta? <a href="login.php">Entrar</a>
    </div>
  </main>
</body>
</html>