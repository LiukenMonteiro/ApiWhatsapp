<?php
/**
 * includes/data_loader.php
 *
 * Carrega os dados necessários para cada aba antes de renderizar a view.
 * Determina qual aba está ativa e consulta a API apenas para os dados
 * necessários àquela aba (evita chamadas desnecessárias).
 *
 * Variáveis exportadas para o escopo global (usadas pelas views):
 *   $whatsapp1            → status da instância 1 do WhatsApp
 *   $whatsapp2            → status da instância 2 do WhatsApp
 *   $aba_ativa            → aba atual: 'todos' | 'buscar' | 'cadastrar' | 'editar' | 'whatsapp'
 *   $todos_pacientes      → lista de pacientes (apenas na aba 'todos')
 *   $termo_busca          → termo digitado na busca (apenas na aba 'buscar')
 *   $pacientes_encontrados → resultado da busca (apenas na aba 'buscar')
 *   $paciente_editar      → dados do paciente a editar (apenas na aba 'editar')
 *
 * Incluído em: index.php
 */
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
