<?php
/**
 * includes/ajax_proxy.php
 *
 * Proxy para requisições AJAX feitas pelo JavaScript do frontend.
 * Intercepta chamadas com o parâmetro GET "ajax_action", repassa
 * para a API Node.js via chamarApi() e devolve o resultado em JSON.
 *
 * Ações disponíveis:
 *   - whatsapp_status      → consulta se a instância está conectada
 *   - whatsapp_qrcode      → obtém o QR Code para conexão
 *   - whatsapp_conectar    → inicia a conexão da instância
 *   - whatsapp_desconectar → encerra a conexão da instância
 *
 * Este arquivo encerra a execução com exit após responder (não renderiza HTML).
 * Incluído em: index.php
 */
if (isset($_GET['ajax_action'])) {
  header('Content-Type: application/json');
  $action = $_GET['ajax_action'];
  $id     = in_array($_GET['id'] ?? '', ['1', '2']) ? $_GET['id'] : '1';

  switch ($action) {
    case 'whatsapp_status':
      echo json_encode(chamarApi('GET', "/whatsapp/{$id}/status"));
      break;
    case 'whatsapp_qrcode':
      echo json_encode(chamarApi('GET', "/whatsapp/{$id}/qrcode"));
      break;
    case 'whatsapp_conectar':
      echo json_encode(chamarApi('POST', "/whatsapp/{$id}/conectar"));
      break;
    case 'whatsapp_desconectar':
      echo json_encode(chamarApi('POST', "/whatsapp/{$id}/desconectar"));
      break;
    default:
      echo json_encode(['sucesso' => false, 'mensagem' => 'Ação desconhecida.']);
  }
  exit;
}
