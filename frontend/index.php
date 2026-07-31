<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/ajax_proxy.php';
require_once __DIR__ . '/includes/form_actions.php';
require_once __DIR__ . '/includes/data_loader.php';

/** @var string $aba_ativa */
/** @var array  $whatsapp1 */
/** @var array  $whatsapp2 */
/** @var string $mensagem_feedback */
/** @var string $tipo_feedback */
/** @var array  $todos_pacientes */
/** @var string $termo_busca */
/** @var array  $pacientes_encontrados */
/** @var array|null $paciente_editar */
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

  <?php require __DIR__ . '/includes/views/header.php'; ?>

  <?php if ($aba_ativa === 'todos'):          require __DIR__ . '/includes/views/aba_todos.php';
  elseif ($aba_ativa === 'buscar'):           require __DIR__ . '/includes/views/aba_buscar.php';
  elseif ($aba_ativa === 'cadastrar'):        require __DIR__ . '/includes/views/aba_cadastrar.php';
  elseif ($aba_ativa === 'editar'):           require __DIR__ . '/includes/views/aba_editar.php';
  elseif ($aba_ativa === 'whatsapp'):         require __DIR__ . '/includes/views/aba_whatsapp.php';
  endif; ?>

</div>

<?php if ($aba_ativa === 'whatsapp'): require __DIR__ . '/includes/views/whatsapp_js.php'; endif; ?>

</body>
</html>
