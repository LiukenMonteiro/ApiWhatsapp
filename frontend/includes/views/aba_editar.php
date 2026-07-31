<section class="card">
  <h2>Editar Paciente</h2>
  <?php if ($paciente_editar): ?>
    <?= renderizarFormulario('editar', $paciente_editar) ?>
  <?php else: ?>
    <p class="sem-resultado">Paciente não encontrado.</p>
  <?php endif; ?>
</section>
