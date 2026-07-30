// ============================================================
// routes/mensagens.js — Rotas de envio de mensagens
// ============================================================

const express         = require('express');
const router          = express.Router();
const db              = require('../config/database');
const whatsappService = require('../services/whatsappService');
const templates       = require('../templates/mensagens');

// ============================================================
// POST /mensagens/exame-pronto
// Body: { "contato_id": 42, "instance_id": "1" }
// ============================================================
router.post('/exame-pronto', async (req, res) => {
  const { contato_id, instance_id = '1' } = req.body;

  if (!contato_id) {
    return res.status(400).json({ sucesso: false, mensagem: 'O campo "contato_id" é obrigatório.' });
  }

  const instancia = ['1', '2'].includes(String(instance_id)) ? String(instance_id) : '1';

  try {
    const [rows] = await db.execute('SELECT * FROM pacientes WHERE id = ?', [contato_id]);

    if (rows.length === 0) {
      return res.status(404).json({ sucesso: false, mensagem: 'Paciente não encontrado.' });
    }

    const paciente = rows[0];
    const mensagem = templates.exame_pronto(paciente);

    await whatsappService.enviarMensagem(instancia, paciente.whatsapp, mensagem);

    return res.json({
      sucesso:  true,
      mensagem: `Mensagem de exame pronto enviada para ${paciente.nome_completo} via WhatsApp ${instancia}.`
    });
  } catch (erro) {
    console.error('Erro ao enviar mensagem de exame pronto:', erro);
    return res.status(500).json({ sucesso: false, mensagem: erro.message || 'Erro interno ao enviar mensagem.' });
  }
});

// ============================================================
// POST /mensagens/pesquisa-satisfacao
// Body: { "contato_id": 42, "instance_id": "1" }
// ============================================================
router.post('/pesquisa-satisfacao', async (req, res) => {
  const { contato_id, instance_id = '1' } = req.body;

  if (!contato_id) {
    return res.status(400).json({ sucesso: false, mensagem: 'O campo "contato_id" é obrigatório.' });
  }

  const instancia = ['1', '2'].includes(String(instance_id)) ? String(instance_id) : '1';

  try {
    const [rows] = await db.execute('SELECT * FROM pacientes WHERE id = ?', [contato_id]);

    if (rows.length === 0) {
      return res.status(404).json({ sucesso: false, mensagem: 'Paciente não encontrado.' });
    }

    const paciente = rows[0];
    const mensagem = templates.pesquisa_satisfacao(paciente);

    await whatsappService.enviarMensagem(instancia, paciente.whatsapp, mensagem);

    return res.json({
      sucesso:  true,
      mensagem: `Pesquisa de satisfação enviada para ${paciente.nome_completo} via WhatsApp ${instancia}.`
    });
  } catch (erro) {
    console.error('Erro ao enviar pesquisa de satisfação:', erro);
    return res.status(500).json({ sucesso: false, mensagem: erro.message || 'Erro interno ao enviar mensagem.' });
  }
});

module.exports = router;
