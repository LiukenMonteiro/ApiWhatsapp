// ============================================================
// routes/whatsapp.js — Gerenciamento de instâncias WhatsApp
// ============================================================

const express = require('express');
const router  = express.Router();
const ws      = require('../services/whatsappService');

function validarId(req, res, next) {
  if (!['1', '2'].includes(req.params.id)) {
    return res.status(400).json({ sucesso: false, mensagem: 'ID inválido. Use 1 ou 2.' });
  }
  next();
}

// GET /whatsapp/:id/status
router.get('/:id/status', validarId, (req, res) => {
  return res.json({ sucesso: true, ...ws.obterStatus(req.params.id) });
});

// GET /whatsapp/:id/qrcode
router.get('/:id/qrcode', validarId, (req, res) => {
  const { id } = req.params;
  const status = ws.obterStatus(id);
  if (status.conectado) return res.json({ sucesso: true, conectado: true });
  const qr = ws.obterQRCode(id);
  if (qr) return res.json({ sucesso: true, conectado: false, qrcode: qr });
  return res.json({ sucesso: true, conectado: false, aguardando: true });
});

// POST /whatsapp/:id/conectar
router.post('/:id/conectar', validarId, async (req, res) => {
  try {
    await ws.resetarInstancia(req.params.id); // limpa estado bloqueado antes de reconectar
    await ws.iniciarConexao(req.params.id);
    return res.json({ sucesso: true, mensagem: `Iniciando conexão WhatsApp ${req.params.id}...` });
  } catch (err) {
    return res.status(500).json({ sucesso: false, mensagem: err.message });
  }
});

// POST /whatsapp/:id/desconectar
router.post('/:id/desconectar', validarId, async (req, res) => {
  try {
    await ws.desconectar(req.params.id);
    return res.json({ sucesso: true, mensagem: `WhatsApp ${req.params.id} desconectado.` });
  } catch (err) {
    return res.status(500).json({ sucesso: false, mensagem: err.message });
  }
});

module.exports = router;
