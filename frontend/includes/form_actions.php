<?php
/**
 * includes/form_actions.php
 *
 * Processa todas as ações enviadas via formulário (método POST).
 * Define as variáveis globais $mensagem_feedback e $tipo_feedback
 * que são exibidas no cabeçalho da página após cada ação.
 *
 * Ações tratadas:
 *   - cadastrar          → cria um novo paciente via API
 *   - atualizar          → atualiza os dados de um paciente existente
 *   - excluir            → remove um paciente pelo ID
 *   - exame_pronto       → envia mensagem WhatsApp de exame pronto
 *   - pesquisa_satisfacao → envia mensagem WhatsApp de pesquisa
 *
 * Incluído em: index.php
 */
function coletarCamposFormulario(): array {
  return [
    'nome_completo'   => trim($_POST['nome_completo']   ?? ''),
    'cpf'             => trim($_POST['cpf']             ?? '') ?: null,
    'data_nascimento' => trim($_POST['data_nascimento'] ?? '') ?: null,
    'whatsapp'        => preg_replace('/\D/', '', $_POST['whatsapp'] ?? ''),
    'telefone_fixo'   => preg_replace('/\D/', '', $_POST['telefone_fixo'] ?? '') ?: null,
    'email'           => trim($_POST['email']           ?? '') ?: null,
    'logradouro'      => trim($_POST['logradouro']      ?? '') ?: null,
    'numero'          => trim($_POST['numero']          ?? '') ?: null,
    'complemento'     => trim($_POST['complemento']     ?? '') ?: null,
    'bairro'          => trim($_POST['bairro']          ?? '') ?: null,
    'cidade'          => trim($_POST['cidade']          ?? '') ?: null,
    'estado'          => trim($_POST['estado']          ?? '') ?: null,
    'observacao'      => trim($_POST['observacao']      ?? '') ?: null,
  ];
}

$mensagem_feedback = '';
$tipo_feedback     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? '';

  if ($acao === 'cadastrar') {
    $dados = coletarCamposFormulario();
    if ($dados['nome_completo'] && $dados['whatsapp']) {
      $resultado = chamarApi('POST', '/contatos', $dados);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    } else {
      $mensagem_feedback = 'Preencha pelo menos Nome Completo e WhatsApp.';
      $tipo_feedback     = 'erro';
    }
  }

  if ($acao === 'atualizar') {
    $id    = (int)($_POST['id'] ?? 0);
    $dados = coletarCamposFormulario();
    if ($id && $dados['nome_completo'] && $dados['whatsapp']) {
      $resultado = chamarApi('PUT', "/contatos/{$id}", $dados);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    } else {
      $mensagem_feedback = 'Preencha pelo menos Nome Completo e WhatsApp.';
      $tipo_feedback     = 'erro';
    }
  }

  if ($acao === 'excluir') {
    $id = (int)($_POST['contato_id'] ?? 0);
    if ($id) {
      $resultado = chamarApi('DELETE', "/contatos/{$id}");
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }

  if ($acao === 'exame_pronto') {
    $id          = (int)($_POST['contato_id'] ?? 0);
    $instance_id = in_array($_POST['instance_id'] ?? '', ['1', '2']) ? $_POST['instance_id'] : '1';
    if ($id) {
      $resultado = chamarApi('POST', '/mensagens/exame-pronto', [
        'contato_id'  => $id,
        'instance_id' => $instance_id,
      ]);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }

  if ($acao === 'pesquisa_satisfacao') {
    $id          = (int)($_POST['contato_id'] ?? 0);
    $instance_id = in_array($_POST['instance_id'] ?? '', ['1', '2']) ? $_POST['instance_id'] : '1';
    if ($id) {
      $resultado = chamarApi('POST', '/mensagens/pesquisa-satisfacao', [
        'contato_id'  => $id,
        'instance_id' => $instance_id,
      ]);
      $mensagem_feedback = $resultado['mensagem'];
      $tipo_feedback     = $resultado['sucesso'] ? 'sucesso' : 'erro';
    }
  }
}
