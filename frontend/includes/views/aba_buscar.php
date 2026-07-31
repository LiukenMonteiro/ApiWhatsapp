<?php
/**
 * includes/views/aba_buscar.php
 *
 * View da aba "Buscar" — permite pesquisar pacientes pelo nome.
 * Exibe um campo de busca e, após o envio, lista os resultados encontrados
 * ou uma mensagem caso nenhum paciente seja encontrado.
 * A busca só é executada com ao menos 2 caracteres (validado em data_loader.php).
 *
 * Variáveis esperadas do escopo global:
 *   $termo_busca           → string com o termo digitado pelo usuário
 *   $pacientes_encontrados → array com os pacientes retornados pela busca
 *
 * Funções utilizadas: renderizarTabela() — definida em includes/helpers.php
 * Incluído em: index.php
 */
?>
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
