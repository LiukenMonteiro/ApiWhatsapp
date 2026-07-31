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
