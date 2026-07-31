<?php
/**
 * includes/views/aba_cadastrar.php
 *
 * View da aba "Cadastrar" — exibe o formulário para criação de um novo paciente.
 * O formulário é enviado via POST para index.php com acao=cadastrar,
 * processado em includes/form_actions.php.
 *
 * Funções utilizadas: renderizarFormulario() — definida em includes/helpers.php
 * Incluído em: index.php
 */
?>
<section class="card">
  <h2>Cadastrar Novo Paciente</h2>
  <?= renderizarFormulario('cadastrar', null) ?>
</section>
