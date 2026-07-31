<?php
require_once 'config.php';
require_once 'includes/helpers.php';
require_once 'includes/ajax_proxy.php';
require_once 'includes/form_actions.php';
require_once 'includes/data_loader.php';
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

  <?php require 'includes/views/header.php'; ?>

  <?php if ($aba_ativa === 'todos'):     require 'includes/views/aba_todos.php';
  elseif ($aba_ativa === 'buscar'):      require 'includes/views/aba_buscar.php';
  elseif ($aba_ativa === 'cadastrar'):   require 'includes/views/aba_cadastrar.php';
  elseif ($aba_ativa === 'editar'):      require 'includes/views/aba_editar.php';
  elseif ($aba_ativa === 'whatsapp'):    require 'includes/views/aba_whatsapp.php';
  endif; ?>

</div>

<?php if ($aba_ativa === 'whatsapp'): require 'includes/views/whatsapp_js.php'; endif; ?>

</body>
</html>
