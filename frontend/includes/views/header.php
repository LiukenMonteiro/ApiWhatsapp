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

<?php if ($mensagem_feedback): ?>
  <div class="alerta alerta-<?= $tipo_feedback ?>">
    <?= htmlspecialchars($mensagem_feedback) ?>
  </div>
<?php endif; ?>

<div class="abas">
  <a href="?aba=todos"     class="aba <?= $aba_ativa === 'todos'     ? 'ativa' : '' ?>">👥 Pacientes</a>
  <a href="?aba=buscar"    class="aba <?= $aba_ativa === 'buscar'    ? 'ativa' : '' ?>">🔍 Buscar</a>
  <a href="?aba=cadastrar" class="aba <?= $aba_ativa === 'cadastrar' ? 'ativa' : '' ?>">➕ Cadastrar</a>
  <a href="?aba=whatsapp"  class="aba <?= $aba_ativa === 'whatsapp'  ? 'ativa' : '' ?>">📱 WhatsApp</a>
  <?php if ($aba_ativa === 'editar'): ?>
    <span class="aba ativa">✏️ Editar</span>
  <?php endif; ?>
</div>
