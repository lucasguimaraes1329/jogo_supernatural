<?php
require_once __DIR__.'/Character.php';
require_once __DIR__.'/Dice.php';
require_once __DIR__.'/Challenge.php';
require_once __DIR__.'/Scene.php';

class GameEngine {
    private array $characters = [];
    private array $scenes = [];
    private array $challenges = [];

    public function __construct() {
        $this->loadCharacters();
        $this->loadChallenges();
        $this->loadScenes();
    }

    private function loadCharacters(): void {
        $base = 'assets/img/personagens/';
        $this->characters = [
            'scooby'   => new Character('scooby',   'Scooby',   'Medroso / intuitivo', 2, $base.'scooby_idle.png',   $base.'scooby_retrato.png'),
            'salsicha' => new Character('salsicha', 'Salsicha', 'Improviso / percepção', 1, $base.'salsicha_idle.png', $base.'salsicha_retrato.png'),
            'velma'    => new Character('velma',    'Velma',    'Investigação / lógica', 3, $base.'velma_idle.png',    $base.'velma_retrato.png'),
            'fred'     => new Character('fred',     'Fred',     'Ação / coragem',        2, $base.'fred_idle.png',     $base.'fred_retrato.png'),
            'daphne'   => new Character('daphne',   'Daphne',   'Observação / agilidade',2, $base.'daphne_idle.png',   $base.'daphne_retrato.png'),
            'diretor'  => new Character('diretor',  'Diretor',  'Suspeito',              0, $base.'diretor_idle.png',  $base.'diretor_retrato.png'),
            'guardiao' => new Character('guardiao', 'Guardião', 'Ameaça',                2, $base.'guardiao_idle.png', $base.'guardiao_retrato.png'),
        ];
    }

    private function loadChallenges(): void {
        $this->challenges = [
            'tower_lock'   => new Challenge('tower_lock',   'A fechadura da torre',     'A trava ritual exige precisão. Role o dado e some a habilidade de Fred.', 5),
            'forest_chase' => new Challenge('forest_chase', 'A corrida na floresta',    'A criatura está atrás de vocês. Daphne precisa guiar o grupo até o gerador.', 6),
            'final_seal'   => new Challenge('final_seal',   'O último selo',            'A máquina tenta fechar o ritual novamente. A lógica de Velma é decisiva.', 7),
        ];
    }

    private function loadScenes(): void {
        $this->scenes = [
            // ========== 1. ENTRADA ==========
            'entrada' => new Scene('entrada', 'A chegada', 'galeria_entrada.png', 'Descobrir por que a cidade foi abandonada.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "A cidade parece ter parado no tempo.\nA galeria municipal é o único prédio ainda iluminado. Relógios da praça marcam horários diferentes."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Os desaparecimentos começaram aqui. Se existe uma explicação racional, vamos encontrá-la.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'O que investigar primeiro?',
                    'options' => [
                        ['label' => 'Examinar a entrada da galeria com cuidado', 'next' => 'entrada_pista', 'clue' => 'Mapa da galeria', 'flag' => 'entrada_investigada'],
                        ['label' => 'Seguir direto para a torre do relógio', 'nextScene' => 'torre', 'flag' => 'rota_torre'],
                    ]
                ],
                'entrada_pista' => [
                    'type' => 'dialogue', 'speaker' => 'Scooby',
                    'text' => 'Ruh-roh... tem cheiro de coisa bem velha por aqui. E de medo.'
                ],
                'choice2' => [
                    'type' => 'choice', 'speaker' => 'Daphne',
                    'question' => 'Uma porta lateral está destrancada. O que fazer?',
                    'options' => [
                        ['label' => 'Entrar pela porta lateral (arquivo)', 'nextScene' => 'arquivo', 'clue' => 'Chave enferrujada'],
                        ['label' => 'Ir para a praça primeiro', 'nextScene' => 'praca'],
                    ]
                ],
            ], ['scooby','velma','fred','daphne','salsicha']),

            // ========== 2. PRAÇA ==========
            'praca' => new Scene('praca', 'A praça vazia', 'rua_galeria.png', 'Encontrar uma pista deixada pelos desaparecidos.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "Na praça, relógios públicos marcam horários diferentes.\nUm deles está parado exatamente às 3:33."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Salsicha',
                    'text' => 'Isso não é normal nem para uma cidade fantasma. Alguém queria que a gente notasse.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Qual relógio examinar?',
                    'options' => [
                        ['label' => 'O relógio quebrado (3:33)', 'next' => 'clock', 'clue' => 'Relógio parado às 3:33'],
                        ['label' => 'A banca de jornais abandonada', 'next' => 'paper', 'clue' => 'Jornal sobre desaparecimentos'],
                    ]
                ],
                'clock' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'A engrenagem foi travada de propósito. Alguém queria que víssemos exatamente 3:33.'
                ],
                'paper' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'Todos os desaparecidos tinham visitado a galeria antes de sumir. E todos mencionam “a névoa azul”.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'Para onde agora?',
                    'options' => [
                        ['label' => 'Investigar a torre do relógio', 'nextScene' => 'torre'],
                        ['label' => 'Ir à mansão Blackwood', 'nextScene' => 'mansao'],
                    ]
                ],
            ], ['velma','daphne','fred','salsicha']),

            // ========== 3. TORRE ==========
            'torre' => new Scene('torre', 'A torre — 3:33', 'torre.png', 'Descobrir quem mantém o mecanismo funcionando.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "As engrenagens da torre continuam girando apesar do corte de energia.\nPegadas recentes levam até uma porta de ferro com símbolos estranhos."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'A porta está presa por um mecanismo antigo. Parece ritualístico… ou só engenharia muito complicada.'
                ],
                'challenge' => [
                    'type' => 'challenge',
                    'challenge' => 'tower_lock',
                    'character' => 'fred',
                    'successNext' => 'tower_success',
                    'failNext' => 'tower_fail',
                    'speaker' => 'Fred'
                ],
                'tower_success' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'A fechadura cedeu. Há uma sala de controle escondida atrás dela — e registros recentes.'
                ],
                'tower_fail' => [
                    'type' => 'dialogue', 'speaker' => 'Salsicha',
                    'text' => 'Talvez bater na porta não tenha sido a melhor ideia… mas conseguimos abrir pela força bruta.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Na sala há dois caminhos. Qual seguir?',
                    'options' => [
                        ['label' => 'Examinar o mecanismo da torre', 'nextScene' => 'laboratorio', 'clue' => 'Registro da torre', 'flag' => 'mecanismo_visto'],
                        ['label' => 'Seguir as pegadas do Guardião', 'nextScene' => 'cemiterio', 'clue' => 'Rastro do Guardião', 'flag' => 'rastro_guardiao'],
                    ]
                ],
            ], ['fred','velma','salsicha','scooby']),

            // ========== 4. MANSÃO ==========
            'mansao' => new Scene('mansao', 'A mansão Blackwood', 'mansao.png', 'Encontrar a passagem subterrânea.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "A mansão está vazia, mas a poeira foi removida de um corredor inteiro.\nAlguém ainda usa este lugar."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'O retrato da família Blackwood parece ter sido movido recentemente. Há marcas no chão.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Daphne',
                    'question' => 'Onde procurar uma passagem secreta?',
                    'options' => [
                        ['label' => 'Atrás do retrato da família', 'next' => 'retrato', 'clue' => 'Retrato deslocado'],
                        ['label' => 'Na biblioteca da mansão', 'nextScene' => 'arquivo', 'clue' => 'Livro sobre a Ordem'],
                    ]
                ],
                'retrato' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Existe uma alavanca escondida. Ela abre uma escada de pedra que desce para o subsolo.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Descer agora ou procurar mais documentos?',
                    'options' => [
                        ['label' => 'Descer pela passagem', 'nextScene' => 'cemiterio', 'flag' => 'desceu_mansao'],
                        ['label' => 'Marcar o local e ir ao arquivo', 'nextScene' => 'arquivo'],
                    ]
                ],
            ], ['daphne','velma','scooby']),

            // ========== 5. CEMITÉRIO ==========
            'cemiterio' => new Scene('cemiterio', 'O cemitério', 'cemiterio.png', 'Encontrar os símbolos que abrem a passagem.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "Três lápides antigas carregam símbolos idênticos aos encontrados na chave da torre.\nLua • Torre • Névoa."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Precisamos descobrir a ordem correta. Uma sequência errada pode ativar alguma armadilha… ou a névoa.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Qual símbolo examinar primeiro?',
                    'options' => [
                        ['label' => 'Símbolo da Lua', 'next' => 'lua', 'clue' => 'Símbolo da Lua'],
                        ['label' => 'Símbolo da Torre', 'next' => 'torre_simbolo', 'clue' => 'Símbolo da Torre'],
                        ['label' => 'Símbolo da Névoa', 'next' => 'nevoa', 'clue' => 'Símbolo da Névoa'],
                    ]
                ],
                'lua' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'A inscrição diz: “A lua observa o caminho antes da torre abrir o céu.”'
                ],
                'torre_simbolo' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'A torre vem depois da lua. Isso combina com o que vimos no mecanismo.'
                ],
                'nevoa' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'A névoa aparece por último. É o resultado, não a causa.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'A sequência parece clara. Qual ordem testar?',
                    'options' => [
                        ['label' => 'Lua → Torre → Névoa (ordem correta)', 'nextScene' => 'laboratorio', 'clue' => 'Ordem dos símbolos', 'flag' => 'ordem_correta'],
                        ['label' => 'Testar outra ordem (arriscado)', 'nextScene' => 'floresta', 'flag' => 'ordem_errada'],
                    ]
                ],
            ], ['velma','daphne','scooby']),

            // ========== 6. LABORATÓRIO ==========
            'laboratorio' => new Scene('laboratorio', 'O laboratório subterrâneo', 'laboratorio.png', 'Descobrir a origem da névoa.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "Tubos antigos ainda produzem uma névoa azulada fraca.\nHá registros de testes datados de décadas atrás — e anotações recentes."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'A névoa é artificial. Mas alguém a misturou com um composto desconhecido… e está usando a torre como difusor.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Qual evidência priorizar?',
                    'options' => [
                        ['label' => 'Os registros de testes e financiamento', 'next' => 'docs', 'clue' => 'Relatório de testes', 'flag' => 'tem_relatorio'],
                        ['label' => 'O painel de energia e geradores', 'next' => 'painel', 'clue' => 'Registro de energia', 'flag' => 'tem_registro'],
                    ]
                ],
                'docs' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'O financiamento veio do museu municipal. O diretor aparece nos primeiros documentos… e nos últimos.'
                ],
                'painel' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'Três geradores alimentam a torre e a produção de névoa. Desligar dois já deve enfraquecer o sistema.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Próximo passo?',
                    'options' => [
                        ['label' => 'Confrontar o diretor no arquivo', 'nextScene' => 'arquivo', 'flag' => 'vai_confrontar'],
                        ['label' => 'Ir direto aos geradores na floresta', 'nextScene' => 'floresta', 'flag' => 'vai_geradores'],
                    ]
                ],
            ], ['velma','fred','salsicha']),

            // ========== 7. ARQUIVO ==========
            'arquivo' => new Scene('arquivo', 'O arquivo municipal', 'arquivo.png', 'Provar a ligação do diretor com a Ordem.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "Caixas de documentos estão empilhadas até o teto.\nUma pasta recente foi escondida entre registros de cinquenta anos atrás."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'Aqui. O mesmo símbolo da torre… e uma assinatura do diretor do museu.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Daphne',
                    'question' => 'O que fazer com a prova?',
                    'options' => [
                        ['label' => 'Guardar como evidência e continuar investigando', 'next' => 'proof', 'clue' => 'Confissão parcial', 'flag' => 'prova_guardada'],
                        ['label' => 'Confrontar o diretor imediatamente', 'nextScene' => 'diretor', 'flag' => 'confronto_diretor'],
                    ]
                ],
                'proof' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Ainda não é suficiente para a polícia. Precisamos mostrar como a máquina funciona e quem controla a névoa.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'Onde conseguir a prova final?',
                    'options' => [
                        ['label' => 'Na floresta, nos geradores', 'nextScene' => 'floresta'],
                        ['label' => 'Voltar à torre e observar o mecanismo', 'nextScene' => 'torre'],
                    ]
                ],
            ], ['daphne','velma','fred']),

            // ========== 8. DIRETOR ==========
            'diretor' => new Scene('diretor', 'O gabinete do diretor', 'escritorio.png', 'Descobrir a verdade antes que ele escape.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Diretor',
                    'text' => 'Vocês chegaram muito longe. Talvez seja melhor parar por aqui… enquanto ainda podem.'
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'A névoa, a torre e os desaparecimentos estão ligados a você. Temos documentos.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Diretor',
                    'question' => 'Como pressioná-lo?',
                    'options' => [
                        ['label' => 'Mostrar os documentos e o relatório', 'next' => 'documentos', 'flag' => 'mostrou_documentos'],
                        ['label' => 'Fingir que ainda não sabemos de nada', 'next' => 'fingimento', 'flag' => 'blefe'],
                    ]
                ],
                'documentos' => [
                    'type' => 'dialogue', 'speaker' => 'Diretor',
                    'text' => 'Esses papéis não significam nada sem o mecanismo. Vocês ainda não entendem o que está em jogo.'
                ],
                'fingimento' => [
                    'type' => 'dialogue', 'speaker' => 'Diretor',
                    'text' => 'Se vocês realmente não sabem de nada… então não há motivo para continuar esta conversa.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'O diretor corre para a saída. O que fazer?',
                    'options' => [
                        ['label' => 'Segui-lo até a floresta', 'nextScene' => 'floresta', 'flag' => 'seguiu_diretor'],
                        ['label' => 'Voltar à torre e preparar uma armadilha', 'nextScene' => 'torre_final', 'flag' => 'preparou_armadilha'],
                    ]
                ],
            ], ['diretor','velma','fred','daphne']),

            // ========== 9. FLORESTA ==========
            'floresta' => new Scene('floresta', 'A floresta sob a névoa', 'floresta.png', 'Alcançar o primeiro gerador.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "A névoa cobre as árvores como um véu azul.\nUm ruído pesado e irregular se aproxima por trás do grupo."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Salsicha',
                    'text' => 'Eu voto por uma retirada estratégica! Tipo… correr muito rápido!'
                ],
                'challenge' => [
                    'type' => 'challenge',
                    'challenge' => 'forest_chase',
                    'character' => 'daphne',
                    'successNext' => 'forest_success',
                    'failNext' => 'forest_fail',
                    'speaker' => 'Daphne'
                ],
                'forest_success' => [
                    'type' => 'dialogue', 'speaker' => 'Daphne',
                    'text' => 'Chegamos ao gerador antes da criatura. Ela ainda está por perto, mas perdemos ela de vista.'
                ],
                'forest_fail' => [
                    'type' => 'dialogue', 'speaker' => 'Salsicha',
                    'text' => 'Quase! Mas conseguimos nos esconder atrás do gerador. A criatura passou por nós.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'Desligar o gerador ou investigar a criatura?',
                    'options' => [
                        ['label' => 'Desligar o gerador agora', 'nextScene' => 'geradores', 'clue' => 'Gerador 1 desligado', 'flag' => 'gerador1'],
                        ['label' => 'Seguir o rastro da criatura', 'nextScene' => 'torre_final', 'clue' => 'Rastro da criatura', 'flag' => 'seguiu_criatura'],
                    ]
                ],
            ], ['daphne','salsicha','fred','scooby']),

            // ========== 10. GERADORES ==========
            'geradores' => new Scene('geradores', 'Os três geradores', 'geradores.png', 'Desligar os geradores restantes.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Um gerador já está desligado. Dois ainda alimentam a torre e a névoa.'
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'Não precisamos explorar cada canto. Precisamos cortar a energia o mais rápido possível.'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'Qual caminho tomar até o segundo gerador?',
                    'options' => [
                        ['label' => 'Atalho pelo depósito abandonado', 'next' => 'atalho', 'clue' => 'Mapa dos geradores'],
                        ['label' => 'Caminho mais seguro pela estrada antiga', 'next' => 'seguro'],
                    ]
                ],
                'atalho' => [
                    'type' => 'dialogue', 'speaker' => 'Scooby',
                    'text' => 'Rápido! Rápido! Scooby-Doo não gosta de ficar perto desses barulhos!'
                ],
                'seguro' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'O caminho é mais longo, mas evitamos a área onde a criatura foi vista pela última vez.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'O segundo gerador foi encontrado. O que fazer?',
                    'options' => [
                        ['label' => 'Desligar e seguir para a torre final', 'nextScene' => 'torre_final', 'clue' => 'Gerador 2 desligado', 'flag' => 'gerador2'],
                        ['label' => 'Voltar ao laboratório para mais dados', 'nextScene' => 'laboratorio'],
                    ]
                ],
            ], ['fred','velma','scooby','salsicha']),

            // ========== 11. TORRE FINAL ==========
            'torre_final' => new Scene('torre_final', 'A torre — 3:33', 'torre_final.png', 'Impedir que o último selo seja reativado.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "3:32.\nO sino começa a vibrar. O Guardião surge diante da máquina, a máscara refletindo a névoa azul."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Guardião',
                    'text' => 'Vocês não entendem o que estão prestes a libertar. O selo não é só uma máquina…'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Velma',
                    'question' => 'Como agir?',
                    'options' => [
                        ['label' => 'Ativar a armadilha preparada por Fred', 'next' => 'armadilha', 'flag' => 'armadilha_ativa'],
                        ['label' => 'Tentar negociar e ouvir a verdade', 'next' => 'negociar', 'flag' => 'negociou'],
                    ]
                ],
                'armadilha' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'Agora! A armadilha prendeu a máquina. Velma, é a sua vez!'
                ],
                'negociar' => [
                    'type' => 'dialogue', 'speaker' => 'Guardião',
                    'text' => 'Se querem a verdade… retirem minha máscara. Mas saibam que o preço será alto.'
                ],
                'challenge' => [
                    'type' => 'challenge',
                    'challenge' => 'final_seal',
                    'character' => 'velma',
                    'successNext' => 'final_success',
                    'failNext' => 'final_fail',
                    'speaker' => 'Velma'
                ],
                'final_success' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'O selo está quebrado. A máquina não consegue mais sustentar a névoa. A pressão caiu.'
                ],
                'final_fail' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Ainda não… mas o mecanismo perdeu força. Temos outra chance se agirmos rápido.'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Fred',
                    'question' => 'Quem deve retirar a máscara do Guardião?',
                    'options' => [
                        ['label' => 'Velma (lógica e provas)', 'nextScene' => 'verdade', 'flag' => 'velma_revela'],
                        ['label' => 'Fred (coragem)', 'nextScene' => 'verdade', 'flag' => 'fred_revela'],
                        ['label' => 'Scooby (intuição)', 'nextScene' => 'verdade', 'flag' => 'scooby_revela'],
                    ]
                ],
            ], ['velma','fred','guardiao','scooby']),

            // ========== 12. VERDADE ==========
            'verdade' => new Scene('verdade', 'A verdade', 'verdade.png', 'Revelar quem estava por trás do mistério.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "A máscara cai.\nPor trás dela está o diretor do museu — pálido, cansado e sem a pose de autoridade."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Diretor',
                    'text' => 'A cidade estava morrendo. Eu precisava de um motivo para esvaziá-la… e a Ordem me deu os símbolos e a névoa.'
                ],
                'b' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Você criou a lenda para comprar os terrenos por um preço baixo. Os desaparecimentos eram… acidentes controlados?'
                ],
                'choice' => [
                    'type' => 'choice', 'speaker' => 'Diretor',
                    'question' => 'Ele oferece um último acordo.',
                    'options' => [
                        ['label' => 'Entregar tudo à polícia e fechar o caso', 'next' => 'policia', 'clue' => 'Confissão do diretor', 'flag' => 'entregou_provas'],
                        ['label' => 'Forçá-lo a revelar o nome da Ordem', 'next' => 'ordem', 'clue' => 'Nome da Ordem', 'flag' => 'descobriu_ordem'],
                    ]
                ],
                'policia' => [
                    'type' => 'dialogue', 'speaker' => 'Fred',
                    'text' => 'Acabou. A cidade terá suas respostas. E a névoa… finalmente vai sumir.'
                ],
                'ordem' => [
                    'type' => 'dialogue', 'speaker' => 'Diretor',
                    'text' => 'A Ordem existe há mais tempo do que eu. Eu apenas usei seus símbolos. Eles ainda estão lá fora…'
                ],
                'end' => [
                    'type' => 'choice', 'speaker' => 'Daphne',
                    'question' => 'Depois da revelação, o grupo decide:',
                    'options' => [
                        ['label' => 'Deixar a cidade em segurança', 'nextScene' => 'final'],
                        ['label' => 'Investigar o símbolo uma última vez (gancho)', 'nextScene' => 'final', 'flag' => 'gancho_final'],
                    ]
                ],
            ], ['diretor','velma','fred','daphne']),

            // ========== 13. FINAL ==========
            'final' => new Scene('final', 'Depois da névoa', 'final.png', 'O mistério foi resolvido.', [
                'start' => [
                    'type' => 'dialogue', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "Ao amanhecer, a névoa desaparece.\nA torre marca 3:34 pela primeira vez em anos."
                ],
                'a' => [
                    'type' => 'dialogue', 'speaker' => 'Salsicha',
                    'text' => 'Então… alguém quer comemorar com um lanche bem generoso?'
                ],
                'b' => [
                    'type' => 'dialogue', 'speaker' => 'Velma',
                    'text' => 'Só depois de catalogarmos todas as pistas. E de checar se a Ordem realmente sumiu…'
                ],
                'end' => [
                    'type' => 'ending', 'speaker' => 'Narrador', 'portrait' => 'narrador',
                    'text' => "FIM — O ÚLTIMO SELO\n\nAlguns mistérios terminam.\nOutros apenas mudam de lugar.\n\nObrigado por jogar."
                ],
            ], ['scooby','salsicha','velma','fred','daphne']),
        ];
    }

    public function scene(string $id): ?Scene {
        return $this->scenes[$id] ?? null;
    }

    public function character(string $id): ?Character {
        return $this->characters[$id] ?? null;
    }

    public function challenge(string $id): ?Challenge {
        return $this->challenges[$id] ?? null;
    }

    public function initialState(): array {
        return [
            'scene'    => 'entrada',
            'node'     => 'start',
            'clues'    => [],
            'flags'    => [],
            'finished' => false,
            'lastRoll' => null,
        ];
    }

    public function publicState(array $state): array {
        $scene = $this->scene($state['scene']) ?? $this->scene('entrada');
        $node  = $scene->node($state['node']) ?? $scene->start();

        if (($node['type'] ?? '') === 'challenge') {
            $ch = $this->challenge($node['challenge']);
            $char = $this->character($node['character'] ?? '');
            $node['title']       = $ch?->title();
            $node['description'] = $ch?->description();
            $node['difficulty']  = $ch?->difficulty();
            $node['characterName'] = $char?->name() ?? 'Personagem';
            $node['skill']       = $char?->skill() ?? 0;
        }

        $characters = array_map(
            fn($id) => $this->character($id)?->toArray(),
            $scene->characters()
        );

        return [
            'scene' => [
                'id'         => $scene->id(),
                'title'      => $scene->title(),
                'background' => $scene->background(),
                'objective'  => $scene->objective(),
                'characters' => array_values(array_filter($characters)),
            ],
            'node'  => $node,
            'state' => $state,
        ];
    }

    /** Mapa de progressão linear dentro de cada cena (diálogo → próximo nó) */
    private function dialogueMap(): array {
        return [
            'entrada'     => ['start' => 'a', 'a' => 'choice', 'entrada_pista' => 'choice2'],
            'praca'       => ['start' => 'a', 'a' => 'choice', 'clock' => 'end', 'paper' => 'end'],
            'torre'       => ['start' => 'a', 'a' => 'challenge', 'tower_success' => 'choice', 'tower_fail' => 'choice'],
            'mansao'      => ['start' => 'a', 'a' => 'choice', 'retrato' => 'end'],
            'cemiterio'   => ['start' => 'a', 'a' => 'choice', 'lua' => 'end', 'torre_simbolo' => 'end', 'nevoa' => 'end'],
            'laboratorio' => ['start' => 'a', 'a' => 'choice', 'docs' => 'end', 'painel' => 'end'],
            'arquivo'     => ['start' => 'a', 'a' => 'choice', 'proof' => 'end'],
            'diretor'     => ['start' => 'a', 'a' => 'choice', 'documentos' => 'end', 'fingimento' => 'end'],
            'floresta'    => ['start' => 'a', 'a' => 'challenge', 'forest_success' => 'choice', 'forest_fail' => 'choice'],
            'geradores'   => ['start' => 'a', 'a' => 'choice', 'atalho' => 'end', 'seguro' => 'end'],
            'torre_final' => ['start' => 'a', 'a' => 'choice', 'armadilha' => 'challenge', 'negociar' => 'challenge', 'final_success' => 'end', 'final_fail' => 'end'],
            'verdade'     => ['start' => 'a', 'a' => 'b', 'b' => 'choice', 'policia' => 'end', 'ordem' => 'end'],
            'final'       => ['start' => 'a', 'a' => 'b', 'b' => 'end'],
        ];
    }

    public function advance(array &$state): array {
        $scene = $this->scene($state['scene']);
        if (!$scene) {
            throw new RuntimeException('Cena inválida.');
        }
        $node = $scene->node($state['node']);
        if (!$node) {
            throw new RuntimeException('Nó inválido.');
        }

        if (($node['type'] ?? '') === 'dialogue') {
            $maps = $this->dialogueMap();
            $next = $maps[$scene->id()][$state['node']] ?? null;
            if ($next && $scene->node($next)) {
                $state['node'] = $next;
            }
        }

        return $this->publicState($state);
    }

    public function choose(array &$state, int $index): array {
        $scene = $this->scene($state['scene']);
        $node  = $scene?->node($state['node']);

        if (!$node || ($node['type'] ?? '') !== 'choice') {
            throw new RuntimeException('Não há escolha disponível.');
        }

        $opt = $node['options'][$index] ?? null;
        if (!$opt) {
            throw new RuntimeException('Escolha inválida.');
        }

        if (isset($opt['clue']) && !in_array($opt['clue'], $state['clues'], true)) {
            $state['clues'][] = $opt['clue'];
        }
        if (isset($opt['flag'])) {
            $state['flags'][$opt['flag']] = true;
        }

        if (isset($opt['nextScene'])) {
            $state['scene'] = $opt['nextScene'];
            $state['node']  = 'start';
        } else {
            $state['node'] = $opt['next'] ?? 'start';
        }

        return $this->publicState($state);
    }

    public function roll(array &$state): array {
        $scene = $this->scene($state['scene']);
        $node  = $scene?->node($state['node']);

        if (!$node || ($node['type'] ?? '') !== 'challenge') {
            throw new RuntimeException('Nenhum desafio de dado está ativo.');
        }

        $challenge = $this->challenge($node['challenge']);
        $character = $this->character($node['character']);
        $result    = $challenge->attempt($character?->skill() ?? 0);

        $state['lastRoll'] = $result;
        $next = $result['success'] ? $node['successNext'] : $node['failNext'];
        $state['node'] = $next;

        if ($result['success']) {
            $state['flags'][$node['challenge'].'_sucesso'] = true;
        } else {
            $state['flags'][$node['challenge'].'_falha'] = true;
        }

        return $this->publicState($state);
    }

    public function startIfNeeded(?array $state): array {
        return is_array($state) && isset($state['scene'], $state['node'])
            ? $state
            : $this->initialState();
    }
}
