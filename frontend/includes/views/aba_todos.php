<?php
/**
 * includes/views/aba_todos.php
 *
 * View da aba "Pacientes" — lista todos os pacientes cadastrados no sistema.
 * Exibe uma tabela com nome, WhatsApp, cidade, observação e botões de ação.
 * Caso não haja pacientes, exibe link direto para a aba de cadastro.
 *
 * Variáveis esperadas do escopo global:
 *   $todos_pacientes → array com todos os pacientes retornados pela API
 *
 * Funções utilizadas: renderizarTabela() — definida em includes/helpers.php
 * Incluído em: index.php
 */
?>
<section class="card">
  <h2>Todos os Pacientes (<?= count($todos_pacientes) ?>)</h2>
  <?php if (count($todos_pacientes) === 0): ?>
    <p class="sem-resultado">Nenhum paciente cadastrado ainda. <a href="?aba=cadastrar">Cadastrar agora →</a></p>
  <?php else: ?>
    <?= renderizarTabela($todos_pacientes, 'todos') ?>
  <?php endif; ?>
</section>
