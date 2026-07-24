// ============================================================
// whatsappService.js — Camada de envio de mensagens WhatsApp
// ============================================================

const {
  default: makeWASocket,
  useMultiFileAuthState,
  DisconnectReason,
  fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');
const pino   = require('pino');
const path   = require('path');

let sock      = null;
let conectado = false;
let tentativas = 0;
const MAX_TENTATIVAS = 5;

async function iniciarConexao() {
  const { state, saveCreds } = await useMultiFileAuthState(
    path.join(__dirname, '../../auth')
  );

  // Busca a versão atual do WhatsApp Web — evita o erro 405
  // que acontece quando o Baileys usa uma versão desatualizada
  const { version } = await fetchLatestBaileysVersion();
  console.log(`📡 Usando WhatsApp Web versão: ${version.join('.')}`);

  sock = makeWASocket({
    version,
    auth:            state,
    logger:          pino({ level: 'silent' }),
    browser:         ['WhatsApp', 'Desktop', '2.2409.2'],
    syncFullHistory: false,
  });

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update;

    // Só loga mudanças relevantes: QR, conectou, desconectou
    if (!qr && !connection) return;

    if (qr) {
      tentativas = 0;
      console.log('\n📱 Escaneie o QR Code abaixo com o WhatsApp Business:\n');
      qrcode.generate(qr, { small: true });
    }

    if (connection === 'open') {
      tentativas = 0;
      conectado  = true;
      console.log('✅ WhatsApp conectado!');
    }

    if (connection === 'close') {
      conectado = false;

      const erro       = lastDisconnect?.error;
      const codigoErro = erro?.output?.statusCode;

      if (codigoErro === DisconnectReason.loggedOut) {
        console.log('❌ Logout. Delete a pasta /auth e reinicie para reconectar.');
        return;
      }

      tentativas++;
      if (tentativas >= MAX_TENTATIVAS) {
        console.log('❌ Não conseguiu conectar após várias tentativas. Reinicie o servidor.');
        return;
      }

      console.log(`⚠️  Conexão perdida — reconectando (${tentativas}/${MAX_TENTATIVAS})...`);
      setTimeout(iniciarConexao, 5000);
    }
  });
}

async function enviarMensagem(numero, texto) {
  if (!conectado || !sock) {
    throw new Error('WhatsApp não está conectado.');
  }
  const numeroFormatado = numero.replace(/\D/g, '') + '@s.whatsapp.net';
  await sock.sendMessage(numeroFormatado, { text: texto });
  return true;
}

function obterStatus() {
  return { conectado };
}

module.exports = { iniciarConexao, enviarMensagem, obterStatus };
