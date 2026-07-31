<?php
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
