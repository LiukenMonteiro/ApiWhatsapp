<?php
require_once 'config.php';

$mensagem_feedback = '';
$tipo_feedback     = '';

// ============================================================
// Proxy AJAX — chamado via fetch() pelo JavaScript
// ============================================================
if (isset($_GET['ajax_action'])) {
  header('Content-Type: application/json');
  $action = $_GET['ajax_action'];
  $id     = in_array($_GET['id'] ?? '', ['1', '2']) ? $_GET['id'] : '1';

  switch ($action) {
    case 'whatsapp_status':
      echo json_encode(chamarApi('GET', "/whatsapp/{$id}/status"));
      break;
    case 'whatsapp_qrcode':
      echo json_encode(chamarApi('GET', "/whatsapp/{$id}/qrcode"));
      break;
    case 'whatsapp_conectar':
      echo json_encode(chamarApi('POST', "/whatsapp/{$id}/conectar"));
      break;
    case 'whatsapp_desconectar':
      echo json_encode(chamarApi('POST', "/whatsapp/{$id}/desconectar"));
      break;
    default:
      echo json_encode(['sucesso' => false, 'mensagem' => 'Ação desconhecida.']);
  }
  exit;
}

// ============================================================
// Coleta os campos do formulário de paciente
// ============================================================
function coletarCamposFormulario(): array {
  return [
    'nome_completo'   => trim($_POST['nome_completo']   ?? ''),
    'cpf'             => trim($_POST['cpf']             ?? '') ?: null,
    'data_nascimento' => trim($_POST['data_nascimento'] ?? '') ?: null,
    'whatsapp'        => preg_replace('/\D/', '', $_POST['whatsapp'] ?? ''),
    'telefone_fixo'   => preg_replace('/\D/', '', $_POST['telefone_fixo'] ?? '') ?: null,
    'email'           => trim($_POST['email']           ?? '') ?: null,
    'logradouro'      => trim($_POST['logradouro']      ?? '') ?: null,
    'numero'          => trim($_POST['numero']          ?? '') ?: null,
    'complemento'     => trim($_POST['complemento']     ?? '') ?: null,
    'bairro'          => trim($_POST['bairro']          ?? '') ?: null,
    'cidade'          => trim($_POST['cidade']          ?? '') ?: null,
    'estado'          => trim($_POST['estado']          ?? '') ?: null,
    'observacao'      => trim($_POST['observacao']      ?? '') ?: null,
  ];
}

// ============================================================
// Processar ações POST dos formulários
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? '';

  if ($acao === 'cadastrar') {
    $dados = coletarCamposFormulario();
    if ($dados['nome_completo'] && $dados['whatsapp']) {
      $resultado = chamarApi('POST', '/contatos', $dados);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    } else {
      $mensagem_feedback = 'Preencha pelo menos Nome Completo e WhatsApp.';
      $tipo_feedback     = 'erro';
    }
  }

  if ($acao === 'atualizar') {
    $id    = (int)($_POST['id'] ?? 0);
    $dados = coletarCamposFormulario();
    if ($id && $dados['nome_completo'] && $dados['whatsapp']) {
      $resultado = chamarApi('PUT', "/contatos/{$id}", $dados);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    } else {
      $mensagem_feedback = 'Preencha pelo menos Nome Completo e WhatsApp.';
      $tipo_feedback     = 'erro';
    }
  }

  if ($acao === 'excluir') {
    $id = (int)($_POST['contato_id'] ?? 0);
    if ($id) {
      $resultado = chamarApi('DELETE', "/contatos/{$id}");
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }

  if ($acao === 'exame_pronto') {
    $id          = (int)($_POST['contato_id'] ?? 0);
    $instance_id = in_array($_POST['instance_id'] ?? '', ['1', '2']) ? $_POST['instance_id'] : '1';
    if ($id) {
      $resultado = chamarApi('POST', '/mensagens/exame-pronto', [
        'contato_id'  => $id,
        'instance_id' => $instance_id,
      ]);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }

  if ($acao === 'pesquisa_satisfacao') {
    $id          = (int)($_POST['contato_id'] ?? 0);
    $instance_id = in_array($_POST['instance_id'] ?? '', ['1', '2']) ? $_POST['instance_id'] : '1';
    if ($id) {
      $resultado = chamarApi('POST', '/mensagens/pesquisa-satisfacao', [
        'contato_id'  => $id,
        'instance_id' => $instance_id,
      ]);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }
}

// ============================================================
// Dados para cada aba
// ============================================================
$status             = chamarApi('GET', '/status');
$whatsapp1          = $status['instancia1'] ?? ['conectado' => false];
$whatsapp2          = $status['instancia2'] ?? ['conectado' => false];

$aba_ativa = $_GET['aba'] ?? 'todos';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao_post = $_POST['acao'] ?? '';
  if ($acao_post === 'cadastrar') $aba_ativa = 'cadastrar';
  elseif ($acao_post === 'atualizar') $aba_ativa = 'todos';
  elseif (in_array($acao_post, ['excluir','exame_pronto','pesquisa_satisfacao'])) $aba_ativa = $_POST['origem_aba'] ?? 'todos';
}

$todos_pacientes = [];
if ($aba_ativa === 'todos') {
  $r = chamarApi('GET', '/contatos');
  $todos_pacientes = $r['contatos'] ?? [];
}

$pacientes_encontrados = [];
$termo_busca = trim($_GET['buscar'] ?? '');
if ($aba_ativa === 'buscar' && strlen($termo_busca) >= 2) {
  $r = chamarApi('GET', '/contatos/buscar?nome=' . urlencode($termo_busca));
  $pacientes_encontrados = $r['contatos'] ?? [];
}

$paciente_editar = null;
if ($aba_ativa === 'editar' && isset($_GET['id'])) {
  $r = chamarApi('GET', '/contatos/' . (int)$_GET['id']);
  $paciente_editar = $r['contato'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laboratório — Painel WhatsApp</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

  <!-- Cabeçalho -->
  <header class="cabecalho">
    <h1>Painel WhatsApp — Laboratório</h1>
    <div class="cabecalho-status">
      <div class="status-badge <?= $whatsapp1['conectado'] ? 'conectado' : 'desconectado' ?>">
        <?= $whatsapp1['conectado'] ? '🟢' : '🔴' ?> WhatsApp 1
      </div>
      <div class="status-badge <?= $whatsapp2['conectado'] ? 'conectado' : 'desconectado' ?>">
        <?= $whatsapp2['conectado'] ? '🟢' : '🔴' ?> WhatsApp 2
      </div>
    </div>
  </header>

  <!-- Feedback de ações -->
  <?php if ($mensagem_feedback): ?>
    <div class="alerta alerta-<?= $tipo_feedback ?>">
      <?= htmlspecialchars($mensagem_feedback) ?>
    </div>
  <?php endif; ?>

  <!-- Abas de navegação -->
  <div class="abas">
    <a href="?aba=todos"      class="aba <?= $aba_ativa === 'todos'      ? 'ativa' : '' ?>">👥 Pacientes</a>
    <a href="?aba=buscar"     class="aba <?= $aba_ativa === 'buscar'     ? 'ativa' : '' ?>">🔍 Buscar</a>
    <a href="?aba=cadastrar"  class="aba <?= $aba_ativa === 'cadastrar'  ? 'ativa' : '' ?>">➕ Cadastrar</a>
    <a href="?aba=whatsapp"   class="aba <?= $aba_ativa === 'whatsapp'   ? 'ativa' : '' ?>">📱 WhatsApp</a>
    <?php if ($aba_ativa === 'editar'): ?>
      <span class="aba ativa">✏️ Editar</span>
    <?php endif; ?>
  </div>

  <!-- ============================================================
       ABA: Todos os pacientes
  ============================================================ -->
  <?php if ($aba_ativa === 'todos'): ?>
  <section class="card">
    <h2>Todos os Pacientes (<?= count($todos_pacientes) ?>)</h2>
    <?php if (count($todos_pacientes) === 0): ?>
      <p class="sem-resultado">Nenhum paciente cadastrado ainda. <a href="?aba=cadastrar">Cadastrar agora →</a></p>
    <?php else: ?>
      <?= renderizarTabela($todos_pacientes, 'todos') ?>
    <?php endif; ?>
  </section>

  <!-- ============================================================
       ABA: Buscar paciente
  ============================================================ -->
  <?php elseif ($aba_ativa === 'buscar'): ?>
  <section class="card">
    <h2>Buscar Paciente</h2>
    <form method="GET" action="">
      <input type="hidden" name="aba" value="buscar">
      <div class="campo campo-busca">
        <input type="text" name="buscar"
               placeholder="Digite o nome do paciente..."
               value="<?= htmlspecialchars($termo_busca) ?>" autofocus>
        <button type="submit" class="btn btn-secundario">Buscar</button>
      </div>
    </form>
    <?php if ($termo_busca && count($pacientes_encontrados) === 0): ?>
      <p class="sem-resultado">Nenhum paciente encontrado para "<?= htmlspecialchars($termo_busca) ?>".</p>
    <?php elseif (count($pacientes_encontrados) > 0): ?>
      <?= renderizarTabela($pacientes_encontrados, 'buscar') ?>
    <?php endif; ?>
  </section>

  <!-- ============================================================
       ABA: Cadastrar novo paciente
  ============================================================ -->
  <?php elseif ($aba_ativa === 'cadastrar'): ?>
  <section class="card">
    <h2>Cadastrar Novo Paciente</h2>
    <?= renderizarFormulario('cadastrar', null) ?>
  </section>

  <!-- ============================================================
       ABA: Editar paciente
  ============================================================ -->
  <?php elseif ($aba_ativa === 'editar'): ?>
  <section class="card">
    <h2>Editar Paciente</h2>
    <?php if ($paciente_editar): ?>
      <?= renderizarFormulario('editar', $paciente_editar) ?>
    <?php else: ?>
      <p class="sem-resultado">Paciente não encontrado.</p>
    <?php endif; ?>
  </section>

  <!-- ============================================================
       ABA: Gerenciamento de WhatsApp
  ============================================================ -->
  <?php elseif ($aba_ativa === 'whatsapp'): ?>
  <section class="card">
    <h2>Conexões WhatsApp</h2>
    <p style="color:#666;font-size:0.88rem;margin-bottom:20px">
      Gerencie as duas instâncias WhatsApp. Conecte via QR Code.
    </p>

    <div class="whatsapp-grid">
      <?php foreach (['1', '2'] as $wid): ?>
      <div class="wa-card" id="wa-card-<?= $wid ?>">
        <div class="wa-card-header">
          <span class="wa-titulo">📱 WhatsApp <?= $wid ?></span>
          <span class="wa-badge" id="wa-badge-<?= $wid ?>">carregando...</span>
        </div>

        <div class="wa-acoes" id="wa-acoes-<?= $wid ?>">
          <!-- Preenchido pelo JavaScript -->
          <div style="color:#999;font-size:0.85rem">Verificando status...</div>
        </div>

        <!-- Área do QR Code -->
        <div class="wa-qr-area" id="wa-qr-<?= $wid ?>" style="display:none">
          <p class="wa-qr-instrucao">Abra o WhatsApp no celular → Dispositivos conectados → Conectar dispositivo</p>
          <img id="wa-qr-img-<?= $wid ?>" src="" alt="QR Code" class="wa-qr-img">
          <p class="wa-qr-aviso">O QR Code expira em 60 segundos. Esta página atualiza automaticamente.</p>
        </div>

      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php endif; ?>

</div>

<!-- ============================================================
     JavaScript para gerenciamento WhatsApp (apenas aba WhatsApp)
============================================================ -->
<?php if ($aba_ativa === 'whatsapp'): ?>
<script>
const POLL_INTERVAL = 3000;
const polls = {};

async function api(action, id, body = null) {
  const url = `?ajax_action=${action}&id=${id}`;
  const opts = body
    ? { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(body) }
    : { method: 'GET' };
  const r = await fetch(url, opts);
  return r.json();
}

function setBadge(id, texto, classe) {
  const b = document.getElementById(`wa-badge-${id}`);
  b.textContent = texto;
  b.className = `wa-badge ${classe}`;
}

function setAcoes(id, html) {
  document.getElementById(`wa-acoes-${id}`).innerHTML = html;
}

function showQR(id, show) {
  document.getElementById(`wa-qr-${id}`).style.display = show ? 'block' : 'none';
}

async function atualizarCard(id) {
  try {
    const status = await api('whatsapp_status', id);

    if (status.conectado) {
      setBadge(id, '🟢 Conectado', 'badge-ok');
      showQR(id, false);
      setAcoes(id, `
        <button onclick="desconectar('${id}')" class="btn btn-vermelho">
          🔌 Desconectar
        </button>
      `);
      stopPoll(id);
      return;
    }

    if (status.aguardandoQR) {
      setBadge(id, '⏳ Aguardando scan', 'badge-aguard');
      setAcoes(id, `
        <button onclick="desconectar('${id}')" class="btn btn-link">Cancelar</button>
      `);
      // Atualiza a imagem do QR
      const qrData = await api('whatsapp_qrcode', id);
      if (qrData.qrcode) {
        document.getElementById(`wa-qr-img-${id}`).src = qrData.qrcode;
        showQR(id, true);
      }
      return;
    }

    // Socket iniciado mas QR ainda não chegou (conectando...)
    if (status.iniciado) {
      setBadge(id, '⏳ Conectando...', 'badge-aguard');
      return; // mantém a UI atual (área de código ou QR visível)
    }

    // Desconectado / não iniciado
    setBadge(id, '🔴 Desconectado', 'badge-off');
    showQR(id, false);
    setAcoes(id, `
      <button onclick="conectar('${id}')" class="btn btn-verde">▶ Conectar via QR Code</button>
    `);
    stopPoll(id);

  } catch (e) {
    setBadge(id, '❓ Erro', 'badge-off');
  }
}

function startPoll(id) {
  stopPoll(id);
  polls[id] = setInterval(() => atualizarCard(id), POLL_INTERVAL);
}

function stopPoll(id) {
  if (polls[id]) { clearInterval(polls[id]); polls[id] = null; }
}

async function conectar(id) {
  setBadge(id, '⏳ Conectando...', 'badge-aguard');
  setAcoes(id, '<span style="color:#999">Iniciando conexão...</span>');
  await api('whatsapp_conectar', id);
  startPoll(id);
  await atualizarCard(id);
}

async function desconectar(id) {
  if (!confirm(`Desconectar WhatsApp ${id}? Será necessário reconectar.`)) return;
  stopPoll(id);
  setBadge(id, '⏳ Desconectando...', 'badge-aguard');
  showQR(id, false);
  await api('whatsapp_desconectar', id);
  await atualizarCard(id);
}

// Inicializa os dois cards ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
  atualizarCard('1');
  atualizarCard('2');
});
</script>
<?php endif; ?>

<?php

// ============================================================
// Renderiza a tabela de pacientes com botões de ação
// ============================================================
function renderizarTabela(array $pacientes, string $origem): string {
  $html = '<table class="tabela-contatos"><thead><tr>
    <th>Nome</th><th>WhatsApp</th><th>Cidade</th><th>Observação</th><th>Ações</th>
  </tr></thead><tbody>';

  foreach ($pacientes as $p) {
    $id           = (int)$p['id'];
    $nome         = htmlspecialchars($p['nome_completo']);
    $nomeEscapado = htmlspecialchars($p['nome_completo'], ENT_QUOTES);

    $html .= "<tr>
      <td>{$nome}</td>
      <td>" . htmlspecialchars($p['whatsapp']) . "</td>
      <td>" . htmlspecialchars($p['cidade'] ?? '—') . "</td>
      <td>" . htmlspecialchars($p['observacao'] ?? '—') . "</td>
      <td class='acoes'>

        <!-- Exame pronto -->
        <form method='POST' class='form-acao'>
          <input type='hidden' name='acao' value='exame_pronto'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <select name='instance_id' class='select-instancia' title='Enviar via WhatsApp'>
            <option value='1'>W1</option>
            <option value='2'>W2</option>
          </select>
          <button type='submit' class='btn btn-verde'
            onclick=\"return confirm('Enviar exame pronto para {$nomeEscapado}?')\">
            ✅ Exame
          </button>
        </form>

        <!-- Pesquisa -->
        <form method='POST' class='form-acao'>
          <input type='hidden' name='acao' value='pesquisa_satisfacao'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <select name='instance_id' class='select-instancia' title='Enviar via WhatsApp'>
            <option value='1'>W1</option>
            <option value='2'>W2</option>
          </select>
          <button type='submit' class='btn btn-amarelo'
            onclick=\"return confirm('Enviar pesquisa para {$nomeEscapado}?')\">
            ⭐ Pesquisa
          </button>
        </form>

        <!-- Editar -->
        <a href='?aba=editar&id={$id}' class='btn btn-azul'>✏️ Editar</a>

        <!-- Excluir -->
        <form method='POST' style='display:inline'>
          <input type='hidden' name='acao' value='excluir'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <button type='submit' class='btn btn-vermelho'
            onclick=\"return confirm('Excluir {$nomeEscapado}? Esta ação não pode ser desfeita.')\">
            🗑️ Excluir
          </button>
        </form>

      </td>
    </tr>";
  }

  $html .= '</tbody></table>';
  return $html;
}

// ============================================================
// Renderiza o formulário de cadastro ou edição
// ============================================================
function renderizarFormulario(string $modo, ?array $p): string {
  $isEdicao = ($modo === 'editar' && $p !== null);
  $acao     = $isEdicao ? 'atualizar' : 'cadastrar';
  $action   = $isEdicao ? "?aba=editar&id={$p['id']}" : '?aba=cadastrar';

  $v = fn(string $campo) => htmlspecialchars($p[$campo] ?? '');

  $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
          'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

  $opcoesUF = '<option value="">—</option>';
  foreach ($ufs as $uf) {
    $sel = (($p['estado'] ?? '') === $uf) ? ' selected' : '';
    $opcoesUF .= "<option value='{$uf}'{$sel}>{$uf}</option>";
  }

  return "
  <form method='POST' action='{$action}'>
    <input type='hidden' name='acao' value='{$acao}'>
    " . ($isEdicao ? "<input type='hidden' name='id' value='{$p['id']}'>" : '') . "

    <p class='secao-titulo'>Dados pessoais</p>
    <div class='grade-2'>
      <div class='campo'>
        <label for='nome_completo'>Nome completo *</label>
        <input type='text' id='nome_completo' name='nome_completo'
               placeholder='Ex: Frederico Viana Santos'
               value='{$v('nome_completo')}' required>
      </div>
      <div class='campo'>
        <label for='cpf'>CPF</label>
        <input type='text' id='cpf' name='cpf'
               placeholder='000.000.000-00' maxlength='14'
               value='{$v('cpf')}'>
      </div>
      <div class='campo'>
        <label for='data_nascimento'>Data de nascimento</label>
        <input type='date' id='data_nascimento' name='data_nascimento'
               value='{$v('data_nascimento')}'>
      </div>
    </div>

    <p class='secao-titulo'>Contato</p>
    <div class='grade-2'>
      <div class='campo'>
        <label for='whatsapp'>WhatsApp * (somente números)</label>
        <input type='text' id='whatsapp' name='whatsapp'
               placeholder='5511999990001' required
               value='{$v('whatsapp')}'>
      </div>
      <div class='campo'>
        <label for='telefone_fixo'>Telefone fixo</label>
        <input type='text' id='telefone_fixo' name='telefone_fixo'
               placeholder='551133330000'
               value='{$v('telefone_fixo')}'>
      </div>
      <div class='campo'>
        <label for='email'>E-mail</label>
        <input type='email' id='email' name='email'
               placeholder='paciente@email.com'
               value='{$v('email')}'>
      </div>
    </div>

    <p class='secao-titulo'>Endereço</p>
    <div class='grade-2'>
      <div class='campo campo-largo'>
        <label for='logradouro'>Rua / Avenida</label>
        <input type='text' id='logradouro' name='logradouro'
               placeholder='Rua das Flores'
               value='{$v('logradouro')}'>
      </div>
      <div class='campo campo-curto'>
        <label for='numero'>Número</label>
        <input type='text' id='numero' name='numero' placeholder='123'
               value='{$v('numero')}'>
      </div>
      <div class='campo'>
        <label for='complemento'>Complemento</label>
        <input type='text' id='complemento' name='complemento'
               placeholder='Apto 42, Bloco B'
               value='{$v('complemento')}'>
      </div>
      <div class='campo'>
        <label for='bairro'>Bairro</label>
        <input type='text' id='bairro' name='bairro' placeholder='Centro'
               value='{$v('bairro')}'>
      </div>
      <div class='campo'>
        <label for='cidade'>Cidade</label>
        <input type='text' id='cidade' name='cidade' placeholder='São Paulo'
               value='{$v('cidade')}'>
      </div>
      <div class='campo campo-curto'>
        <label for='estado'>UF</label>
        <select id='estado' name='estado'>{$opcoesUF}</select>
      </div>
    </div>

    <p class='secao-titulo'>Observações</p>
    <div class='campo'>
      <label for='observacao'>Observação / tipo de exame</label>
      <input type='text' id='observacao' name='observacao'
             placeholder='Ex: Hemograma completo'
             value='{$v('observacao')}'>
    </div>

    <div class='rodape-form'>
      <span class='legenda-obrigatorio'>* campos obrigatórios</span>
      <div style='display:flex;gap:10px'>
        " . ($isEdicao ? "<a href='?aba=todos' class='btn btn-secundario'>Cancelar</a>" : '') . "
        <button type='submit' class='btn btn-primario'>
          " . ($isEdicao ? '💾 Salvar Alterações' : 'Cadastrar Paciente') . "
        </button>
      </div>
    </div>
  </form>";
}
?>

</body>
</html>
