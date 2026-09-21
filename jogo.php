<?php
require_once __DIR__.'/includes/Auth.php';
Auth::requireLogin();
$username = Auth::username();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scooby-doo</title>
</head>
<body>
    <div id="game">
    <header class="topbar">
      <div>
        <strong>SCOOBY-DOO</strong>
        <span id="chapter">CAPÍTULO 1</span>
      </div>
      <div class="actions">
        <span id="clues" title="Tecla I">PISTAS 0</span>
        <span style="color:#88938d;font-size:11px"><?= htmlspecialchars($username) ?></span>
        <button id="save">SALVAR</button>
          <button onclick="location.href='index.php'">MENU</button>
      </div>
    </header>
    <main class="screen">
      <div id="scene" class="scene">
        <div id="sceneTitle" class="scene-title"></div>
        <div id="sprites" class="sprites"></div>
        <div class="fade"></div>
        <div id="dialogue" class="dialogue">
          <div class="portrait">
            <img id="portrait" src="assets/img/personagens/narrador_retrato.png" alt="">
          </div>
          <div class="panel">
            <div id="speaker" class="speaker">NARRADOR</div>
            <div id="text" class="text"></div>
            <div id="choices" class="choices"></div>
            <div id="roll" class="roll hidden"></div>
            <div id="hint" class="hint">ESPAÇO / ENTER — CONTINUAR</div>
          </div>
          </div>
          <aside id="cluePanel" class="clue-panel hidden">
          <h3>PISTAS</h3>
          <div id="clueList"></div>
        </aside>
      </div>
    </main>
  </div>
</body>
</html>
