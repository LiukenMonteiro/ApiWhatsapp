<?php
/**
 * includes/views/whatsapp_js.php
 *
 * JavaScript de gerenciamento das instâncias WhatsApp.
 * Carregado apenas quando a aba "WhatsApp" está ativa.
 *
 * Responsabilidades:
 *   - Consultar o status de cada instância a cada 3 segundos (polling)
 *   - Atualizar os badges e botões de acordo com o estado (conectado, aguardando QR, desconectado)
 *   - Buscar e exibir o QR Code quando a instância está aguardando conexão
 *   - Iniciar e encerrar conexões via botões
 *
 * Todas as chamadas ao backend são feitas via fetch() para includes/ajax_proxy.php
 * usando o parâmetro GET "ajax_action".
 *
 * Incluído em: index.php (apenas quando $aba_ativa === 'whatsapp')
 */
?>
<script>
const POLL_INTERVAL = 3000;
const polls = {};

async function api(action, id, body = null) {
  const url = `?ajax_action=${action}&id=${id}`;
  const opts = body
    ? { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(body) }
    : { method: 'GET' };
  const r = await fetch(url, opts);
  return r.json();
}

function setBadge(id, texto, classe) {
  const b = document.getElementById(`wa-badge-${id}`);
  b.textContent = texto;
  b.className = `wa-badge ${classe}`;
}

function setAcoes(id, html) {
  document.getElementById(`wa-acoes-${id}`).innerHTML = html;
}

function showQR(id, show) {
  document.getElementById(`wa-qr-${id}`).style.display = show ? 'block' : 'none';
}

async function atualizarCard(id) {
  try {
    const status = await api('whatsapp_status', id);

    if (status.conectado) {
      setBadge(id, '🟢 Conectado', 'badge-ok');
      showQR(id, false);
      setAcoes(id, `
        <button onclick="desconectar('${id}')" class="btn btn-vermelho">
          🔌 Desconectar
        </button>
      `);
      stopPoll(id);
      return;
    }

    if (status.aguardandoQR) {
      setBadge(id, '⏳ Aguardando scan', 'badge-aguard');
      setAcoes(id, `
        <button onclick="desconectar('${id}')" class="btn btn-link">Cancelar</button>
      `);
      const qrData = await api('whatsapp_qrcode', id);
      if (qrData.qrcode) {
        document.getElementById(`wa-qr-img-${id}`).src = qrData.qrcode;
        showQR(id, true);
      }
      return;
    }

    if (status.iniciado) {
      setBadge(id, '⏳ Conectando...', 'badge-aguard');
      return;
    }

    setBadge(id, '🔴 Desconectado', 'badge-off');
    showQR(id, false);
    setAcoes(id, `
      <button onclick="conectar('${id}')" class="btn btn-verde">▶ Conectar via QR Code</button>
    `);
    stopPoll(id);

  } catch (e) {
    setBadge(id, '❓ Erro', 'badge-off');
  }
}

function startPoll(id) {
  stopPoll(id);
  polls[id] = setInterval(() => atualizarCard(id), POLL_INTERVAL);
}

function stopPoll(id) {
  if (polls[id]) { clearInterval(polls[id]); polls[id] = null; }
}

async function conectar(id) {
  setBadge(id, '⏳ Conectando...', 'badge-aguard');
  setAcoes(id, '<span style="color:#999">Iniciando conexão...</span>');
  await api('whatsapp_conectar', id);
  startPoll(id);
  await atualizarCard(id);
}

async function desconectar(id) {
  if (!confirm(`Desconectar WhatsApp ${id}? Será necessário reconectar.`)) return;
  stopPoll(id);
  setBadge(id, '⏳ Desconectando...', 'badge-aguard');
  showQR(id, false);
  await api('whatsapp_desconectar', id);
  await atualizarCard(id);
}

document.addEventListener('DOMContentLoaded', () => {
  atualizarCard('1');
  atualizarCard('2');
});
</script>
