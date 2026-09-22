<?php
require_once __DIR__.'/includes/Auth.php';
Auth::startSession();
$logged = Auth::check();
$username = Auth::username();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Supernatural: O Último Selo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="title">
  <div class="grain"></div>
  <main class="title-card">
    <div class="eyebrow">AVENTURA NARRATIVA • INVESTIGAÇÃO • TERROR</div>
    <h1>O ÚLTIMO<br><span>SELO</span></h1>
    <p>Uma história de mistério em que as cenas só mudam quando suas escolhas e seus dados determinam o próximo passo.</p>

    <?php if ($logged): ?>
      <p style="color:#d5c45c;font-size:13px;margin-bottom:16px">Olá, <strong><?= htmlspecialchars($username) ?></strong></p>
      <div class="menu">
        <button id="newGame">NOVO JOGO</button>
        <button id="continueGame">CONTINUAR</button>
      </div>
      <div class="menu" style="margin-top:8px">
        <button onclick="location.href='logout.php'" style="opacity:.75">SAIR DA CONTA</button>
      </div>
    <?php else: ?>
      <div class="menu">
        <button onclick="location.href='login.php'">ENTRAR</button>
        <button onclick="location.href='registro.php'">CADASTRAR</button>
      </div>
      <p style="color:#707b75;font-size:12px;margin-top:12px">Faça login ou crie uma conta para salvar seu progresso.</p>
    <?php endif; ?>

    <small style="display:block;margin-top:20px">PHP + MySQL • Orientação a Objetos • Progresso por usuário</small>
  </main>

  <?php if ($logged): ?>
  <script>
    async function go(a) {
      await fetch('api/action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: a })
      });
      location.href = 'game.php';
    }
    document.getElementById('newGame').onclick = () => go('new');
    document.getElementById('continueGame').onclick = () => location.href = 'game.php';
  </script>
  <?php endif; ?>
</body>
</html>
