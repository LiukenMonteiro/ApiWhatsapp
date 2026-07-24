// ============================================================
// routes/status.js — Rota de status da conexão WhatsApp
// ============================================================
// Em Express, "Router" é como um agrupador de rotas.
// É parecido com um "controller" no Laravel/CodeIgniter.
// ============================================================

const express = require('express');
const router  = express.Router();
const whatsappService = require('../services/whatsappService');

// GET /status — retorna se o WhatsApp está conectado
// "req" = request (dados que chegaram), "res" = response (o que vamos devolver)
router.get('/', (req, res) => {
  const status = whatsappService.obterStatus();

  return res.json({
    sucesso: true,
    conectado: status.conectado,
    mensagem: status.conectado
      ? 'WhatsApp conectado e pronto para enviar mensagens.'
      : 'WhatsApp desconectado. Verifique o QR Code no terminal.'
  });
});

module.exports = router;
