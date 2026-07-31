<?php
$status    = chamarApi('GET', '/status');
$whatsapp1 = $status['instancia1'] ?? ['conectado' => false];
$whatsapp2 = $status['instancia2'] ?? ['conectado' => false];

$aba_ativa = $_GET['aba'] ?? 'todos';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao_post = $_POST['acao'] ?? '';
  if ($acao_post === 'cadastrar') $aba_ativa = 'cadastrar';
  elseif ($acao_post === 'atualizar') $aba_ativa = 'todos';
  elseif (in_array($acao_post, ['excluir','exame_pronto','pesquisa_satisfacao'])) $aba_ativa = $_POST['origem_aba'] ?? 'todos';
}

$todos_pacientes = [];
if ($aba_ativa === 'todos') {
  $r = chamarApi('GET', '/contatos');
  $todos_pacientes = $r['contatos'] ?? [];
}

$pacientes_encontrados = [];
$termo_busca = trim($_GET['buscar'] ?? '');
if ($aba_ativa === 'buscar' && strlen($termo_busca) >= 2) {
  $r = chamarApi('GET', '/contatos/buscar?nome=' . urlencode($termo_busca));
  $pacientes_encontrados = $r['contatos'] ?? [];
}

$paciente_editar = null;
if ($aba_ativa === 'editar' && isset($_GET['id'])) {
  $r = chamarApi('GET', '/contatos/' . (int)$_GET['id']);
  $paciente_editar = $r['contato'] ?? null;
}
