// ============================================================
// routes/qrcode.js — Página web com o QR Code do WhatsApp
// ============================================================

const express         = require('express');
const router          = express.Router();
const QRCode          = require('qrcode');
const whatsappService = require('../services/whatsappService');

// GET /qrcode — página HTML com o QR Code para escanear
// Atualiza automaticamente a cada 5 segundos enquanto aguarda
router.get('/', async (req, res) => {
  const status = whatsappService.obterStatus();

  // Se já estiver conectado, mostra mensagem de sucesso
  if (status.conectado) {
    return res.send(`<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
      <title>WhatsApp</title>
      <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f0f2f5}
      .box{text-align:center;background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.1)}
      h2{color:#25d366}p{color:#555}</style></head>
      <body><div class="box"><h2>✅ WhatsApp Conectado!</h2>
      <p>A API está pronta para enviar mensagens.</p></div></body></html>`);
  }

  const qrTexto = whatsappService.obterQR();

  // QR ainda não chegou — aguarda e atualiza a página
  if (!qrTexto) {
    return res.send(`<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
      <meta http-equiv="refresh" content="3">
      <title>Aguardando QR Code...</title>
      <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f0f2f5}
      .box{text-align:center;background:#fff;padding:40px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.1)}
      .spinner{width:40px;height:40px;border:4px solid #eee;border-top-color:#25d366;border-radius:50%;animation:spin .8s linear infinite;margin:20px auto}
      @keyframes spin{to{transform:rotate(360deg)}}p{color:#555}</style></head>
      <body><div class="box"><div class="spinner"></div>
      <p>Aguardando QR Code do WhatsApp...<br>Esta página atualiza sozinha.</p></div></body></html>`);
  }

  // Converte o texto do QR em imagem PNG (data URL)
  const qrImagemDataUrl = await QRCode.toDataURL(qrTexto, {
    width: 300,
    margin: 2,
    color: { dark: '#000', light: '#fff' },
  });

  res.send(`<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="30">
  <title>QR Code — WhatsApp</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; display: flex; align-items: center;
           justify-content: center; min-height: 100vh; margin: 0; background: #f0f2f5; }
    .box { text-align: center; background: #fff; padding: 36px 48px;
           border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
    h2   { color: #128c7e; margin-bottom: 4px; }
    .sub { color: #888; font-size: .9rem; margin-bottom: 24px; }
    img  { display: block; margin: 0 auto; border: 1px solid #eee; border-radius: 8px; }
    .instrucoes { margin-top: 24px; text-align: left; background: #f9f9f9;
                  padding: 16px 20px; border-radius: 8px; font-size: .88rem; color: #444; }
    .instrucoes ol { padding-left: 18px; line-height: 2; }
    .aviso { margin-top: 16px; font-size: .8rem; color: #aaa; }
  </style>
</head>
<body>
  <div class="box">
    <h2>📱 Conectar WhatsApp</h2>
    <p class="sub">Escaneie o código abaixo com o WhatsApp Business</p>
    <img src="${qrImagemDataUrl}" alt="QR Code WhatsApp" width="300" height="300">
    <div class="instrucoes">
      <ol>
        <li>Abra o <strong>WhatsApp Business</strong> no celular</li>
        <li>Toque em <strong>⋮ Menu &rsaquo; Aparelhos conectados</strong></li>
        <li>Toque em <strong>Conectar um aparelho</strong></li>
        <li>Aponte a câmera para este QR Code</li>
      </ol>
    </div>
    <p class="aviso">Esta página atualiza automaticamente a cada 30 segundos.</p>
  </div>
</body>
</html>`);
});

module.exports = router;
