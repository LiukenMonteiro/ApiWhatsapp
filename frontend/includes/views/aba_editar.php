<?php
/**
 * includes/views/aba_editar.php
 *
 * View da aba "Editar" — exibe o formulário pré-preenchido para edição de um paciente.
 * O paciente é identificado pelo parâmetro GET "id" e carregado em data_loader.php.
 * O formulário é enviado via POST com acao=atualizar, processado em form_actions.php.
 * Exibe mensagem de erro caso o paciente não seja encontrado.
 *
 * Variáveis esperadas do escopo global:
 *   $paciente_editar → array com os dados do paciente, ou null se não encontrado
 *
 * Funções utilizadas: renderizarFormulario() — definida em includes/helpers.php
 * Incluído em: index.php
 */
?>
<section class="card">
  <h2>Editar Paciente</h2>
  <?php if ($paciente_editar): ?>
    <?= renderizarFormulario('editar', $paciente_editar) ?>
  <?php else: ?>
    <p class="sem-resultado">Paciente não encontrado.</p>
  <?php endif; ?>
</section>
