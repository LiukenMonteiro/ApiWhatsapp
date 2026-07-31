<section class="card">
  <h2>Todos os Pacientes (<?= count($todos_pacientes) ?>)</h2>
  <?php if (count($todos_pacientes) === 0): ?>
    <p class="sem-resultado">Nenhum paciente cadastrado ainda. <a href="?aba=cadastrar">Cadastrar agora →</a></p>
  <?php else: ?>
    <?= renderizarTabela($todos_pacientes, 'todos') ?>
  <?php endif; ?>
</section>
