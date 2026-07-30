// ============================================================
// routes/status.js — Status das instâncias WhatsApp
// ============================================================

const express         = require('express');
const router          = express.Router();
const whatsappService = require('../services/whatsappService');

// GET /status
router.get('/', (req, res) => {
  const s1 = whatsappService.obterStatus('1');
  const s2 = whatsappService.obterStatus('2');

  return res.json({
    sucesso:    true,
    conectado:  s1.conectado, // compatibilidade com versão anterior
    instancia1: s1,
    instancia2: s2,
    mensagem:   s1.conectado
      ? 'WhatsApp 1 conectado e pronto para enviar mensagens.'
      : 'WhatsApp 1 desconectado. Acesse a aba WhatsApp para conectar.',
  });
});

module.exports = router;
