<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Entrar — O Último Selo</title>
  <link rel="stylesheet" href="assets/css/style.css">
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
    <div class="eyebrow">SUPERNATURAL • O ÚLTIMO SELO</div>
    <h1 style="font-size:clamp(36px,7vw,64px)">ENTRAR</h1>
    <?php if ($error): ?><div class="auth-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
      <div>
        <label for="username">USUÁRIO</label>
        <input type="text" id="username" name="username" required autofocus
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username">
      </div>
      <div>
        <label for="password">SENHA</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" style="margin-top:8px;padding:13px 24px;background:#1c2927;border:1px solid #7b8580;color:#fff">
        ENTRAR
      </button>
    </form>
    <div class="auth-links">
      Não tem conta? <a href="register.php">Cadastre-se</a>
    </div>
  </main>
</body>
</html>

