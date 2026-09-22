const $ = id => document.getElementById(id);
let data = null, busy = false;

const positions = [18, 34, 50, 66, 82];

async function api(action, extra = {}) {
  if (busy) return null;
  busy = true;
  try {
    const r = await fetch('api/action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, ...extra })
    });
    const j = await r.json();
    if (!j.ok) throw new Error(j.error || 'Erro');
    data = j.data;
    render();
    return data;
  } catch (e) {
    alert(e.message);
  } finally {
    busy = false;
  }
}

async function load() {
  try {
    const r = await fetch('api/load.php');
    const j = await r.json();
    if (!j.ok) {
      alert('Não foi possível carregar o jogo. Verifique o banco de dados.');
      return;
    }
    data = j.data;
    render();
  } catch (e) {
    alert('Erro de conexão com o servidor.');
  }
}

function asset(path) {
  return path.startsWith('assets/') ? path : 'assets/img/cenarios/' + path;
}

function render() {
  if (!data) return;

  const s = data.scene;
  const n = data.node;
  const state = data.state;

  // Fundo da cena
  $('scene').style.backgroundImage = `url('${asset(s.background)}')`;

  // Cabeçalho
  $('chapter').textContent = `CENA ${sceneNumber(s.id)} — ${s.title.toUpperCase()}`;
  $('sceneTitle').textContent = s.objective;
  $('clues').textContent = `PISTAS ${state.clues.length}`;

  // Sprites dos personagens
  renderSprites(s.characters, n.speaker);

  // Diálogo
  $('speaker').textContent = (n.speaker || 'Narrador').toUpperCase();
  const portrait = n.portrait === 'narrador'
    ? 'assets/img/personagens/narrador_retrato.png'
    : speakerPortrait(n.speaker, s.characters);
  $('portrait').src = portrait;

  $('text').textContent = n.text || n.question || n.description || '';

  // Limpa área de escolhas / dados
  $('choices').innerHTML = '';
  $('roll').innerHTML = '';
  $('roll').classList.add('hidden');

  if (n.type === 'choice') {
    renderChoices(n.options);
  } else if (n.type === 'challenge') {
    renderChallenge(n);
  } else if (n.type === 'ending') {
    $('hint').textContent = 'FIM DA HISTÓRIA — OBRIGADO POR JOGAR';
  } else {
    $('hint').textContent = 'ESPAÇO / ENTER — CONTINUAR';
  }

  // Mostra resultado do último dado se existir
  if (state.lastRoll && (n.type === 'dialogue' || n.type === 'challenge')) {
    renderLastRoll(state.lastRoll);
  }

  renderClues(state.clues);
}

function sceneNumber(id) {
  const order = [
    'entrada', 'praca', 'torre', 'mansao', 'cemiterio',
    'laboratorio', 'arquivo', 'diretor', 'floresta',
    'geradores', 'torre_final', 'verdade', 'final'
  ];
  return Math.max(1, order.indexOf(id) + 1);
}

function speakerPortrait(name, cast) {
  const map = {
    Scooby: 'scooby',
    Salsicha: 'salsicha',
    Velma: 'velma',
    Fred: 'fred',
    Daphne: 'daphne',
    Diretor: 'diretor',
    'Guardião': 'guardiao'
  };
  const key = map[name];
  return key
    ? `assets/img/personagens/${key}_retrato.png`
    : 'assets/img/personagens/narrador_retrato.png';
}

function renderSprites(cast, activeSpeaker) {
  const box = $('sprites');
  box.innerHTML = '';

  cast.forEach((c, i) => {
    const el = document.createElement('img');
    el.src = c.sprite.startsWith('assets/') ? c.sprite : 'assets/img/personagens/' + c.sprite;
    el.className = 'sprite' + (c.name === activeSpeaker ? ' active' : ' dim');
    el.style.left = (positions[i] || 50) + '%';
    el.style.bottom = '22%';
    el.alt = c.name;
    el.title = c.name + ' — ' + (c.role || '');
    box.appendChild(el);
  });
}

function renderChoices(options) {
  $('hint').textContent = 'ESCOLHA UMA OPÇÃO';
  options.forEach((o, i) => {
    const b = document.createElement('button');
    b.innerHTML = `<span>▶</span> ${escapeHtml(o.label)}`;
    b.onclick = () => api('choose', { index: i });
    $('choices').appendChild(b);
  });
}

function renderChallenge(n) {
  $('hint').textContent = 'DESAFIO DE DADOS';

  const info = document.createElement('div');
  info.className = 'challenge-info';
  info.innerHTML = `
    <strong>${escapeHtml(n.title || 'Desafio')}</strong><br>
    ${escapeHtml(n.description || '')}<br>
    <span class="diff">Dificuldade: ${n.difficulty} &nbsp;|&nbsp; Personagem: ${escapeHtml(n.characterName || '')} (bônus +${n.skill || 0})</span>
  `;
  $('roll').appendChild(info);

  const b = document.createElement('button');
  b.className = 'roll-btn';
  b.textContent = 'LANÇAR D6';
  b.onclick = () => api('roll');
  $('roll').appendChild(b);

  $('roll').classList.remove('hidden');
}

function renderLastRoll(r) {
  const el = document.createElement('div');
  el.className = 'roll-result ' + (r.success ? 'success' : 'fail');
  el.innerHTML = `
    DADO: <strong>${r.roll}</strong>
    + BÔNUS: <strong>${r.bonus}</strong>
    = <strong>${r.total}</strong>
    / ${r.difficulty}
    — ${r.success ? 'SUCESSO' : 'FALHA'}
  `;
  $('roll').appendChild(el);
  $('roll').classList.remove('hidden');
}

function renderClues(clues) {
  const list = $('clueList');
  if (!clues.length) {
    list.innerHTML = '<em>Nenhuma pista encontrada ainda.</em>';
    return;
  }
  list.innerHTML = clues.map(c => `<div>◆ ${escapeHtml(c)}</div>`).join('');
}

function toggleClues() {
  $('cluePanel').classList.toggle('hidden');
}

function advance() {
  if (!data) return;
  const t = data.node.type;
  if (t === 'choice' || t === 'challenge' || t === 'ending') return;
  api('advance');
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[m]));
}

// Controles
document.addEventListener('keydown', e => {
  if (e.key === 'i' || e.key === 'I') toggleClues();
  if (e.key === 'Escape') $('cluePanel').classList.add('hidden');
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    advance();
  }
});

$('save').onclick = () => {
  api('save').then(() => alert('Progresso salvo na sua conta.'));
};

// Clique no painel de pistas também fecha
$('cluePanel')?.addEventListener('click', e => {
  if (e.target.id === 'cluePanel') toggleClues();
});

load();
