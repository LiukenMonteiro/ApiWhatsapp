// ============================================================
// whatsappService.js — Suporte a múltiplas instâncias WhatsApp
// ============================================================

const {
  default: makeWASocket,
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const pino   = require('pino');
const path   = require('path');
const fs     = require('fs');

const MAX_TENTATIVAS = 5;

// Map id -> { sock, conectado, qrCode, tentativas, bloqueado }
const instances = new Map();

function getInst(id) {
  if (!instances.has(id)) {
    instances.set(id, { sock: null, conectado: false, qrCode: null, tentativas: 0, bloqueado: false });
  }
  return instances.get(id);
}

function authDir(id) {
  // Instância 1 usa auth/ (compatibilidade com versão anterior)
  // Instância 2 usa auth2/
  return id === '1'
    ? path.join(__dirname, '../../auth')
    : path.join(__dirname, `../../auth${id}`);
}

async function iniciarConexao(id = '1') {
  const inst = getInst(id);
  if (inst.sock || inst.bloqueado) return; // já em execução ou aguardando ação manual

  const { state, saveCreds } = await useMultiFileAuthState(authDir(id));
  const { version } = await fetchLatestBaileysVersion();
  console.log(`📡 [WhatsApp ${id}] Versão Web: ${version.join('.')}`);

  const sock = makeWASocket({
    version,
    auth:            state,
    logger:          pino({ level: 'silent' }),
    browser:         ['WhatsApp', 'Desktop', '2.2409.2'],
    syncFullHistory: false,
  });

  inst.sock = sock;

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;
    if (!qr && !connection) return;

    if (qr) {
      inst.tentativas = 0;
      inst.qrCode = await QRCode.toDataURL(qr);
      console.log(`📱 [WhatsApp ${id}] QR Code gerado.`);
    }

    if (connection === 'open') {
      inst.tentativas = 0;
      inst.conectado  = true;
      inst.qrCode     = null;
      console.log(`✅ [WhatsApp ${id}] Conectado!`);
    }

    if (connection === 'close') {
      inst.conectado = false;
      inst.sock      = null;

      const codigoErro = lastDisconnect?.error?.output?.statusCode;

      if (codigoErro === DisconnectReason.loggedOut) {
        console.log(`❌ [WhatsApp ${id}] Deslogado. Reconecte pelo painel.`);
        inst.bloqueado = true; // impede reconexão automática por timeouts pendentes
        return;
      }

      inst.tentativas++;
      if (inst.tentativas >= MAX_TENTATIVAS) {
        console.log(`❌ [WhatsApp ${id}] Falha ao conectar. Tente novamente pelo painel.`);
        instances.delete(id);
        return;
      }

      console.log(`⚠️  [WhatsApp ${id}] Reconectando (${inst.tentativas}/${MAX_TENTATIVAS})...`);
      setTimeout(() => iniciarConexao(id), 5000);
    }
  });
}


async function resetarInstancia(id) {
  const inst = instances.get(id);
  if (!inst) return;
  if (inst.sock) {
    try { inst.sock.ev.removeAllListeners(); } catch (_) {}
    try { inst.sock.ws?.close(); } catch (_) {}
    inst.sock = null;
  }
  instances.delete(id); // cria estado limpo na próxima chamada a getInst()
}

async function desconectar(id = '1') {
  const inst = instances.get(id);
  if (!inst) return;
  if (inst.sock) {
    try { await inst.sock.logout(); } catch (_) {}
    inst.sock = null;
  }
  instances.delete(id);
}

async function enviarMensagem(id = '1', numero, texto) {
  const inst = instances.get(id);
  if (!inst?.conectado || !inst.sock) {
    throw new Error(`WhatsApp ${id} não está conectado.`);
  }
  const jid = numero.replace(/\D/g, '') + '@s.whatsapp.net';
  await inst.sock.sendMessage(jid, { text: texto });
  return true;
}

function obterStatus(id = '1') {
  const inst = instances.get(id);
  return {
    conectado:    inst?.conectado ?? false,
    aguardandoQR: !!(inst?.qrCode && !inst?.conectado),
    iniciado:     !!(inst?.sock),
  };
}

function obterQRCode(id = '1') {
  return instances.get(id)?.qrCode ?? null;
}

async function iniciarTodasConexoes() {
  // Só tenta reconectar instâncias que já têm sessão salva
  const tarefas = ['1', '2']
    .filter(id => fs.existsSync(authDir(id)))
    .map(id => iniciarConexao(id));
  if (tarefas.length) await Promise.allSettled(tarefas);
}

module.exports = {
  iniciarConexao,
  iniciarTodasConexoes,
  enviarMensagem,
  obterStatus,
  obterQRCode,
  resetarInstancia,
  desconectar,
};
