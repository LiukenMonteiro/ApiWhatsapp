// ============================================================
// routes/contatos.js — Rotas de gestão de pacientes
// ============================================================

const express = require('express');
const router  = express.Router();
const db      = require('../config/database');

// ============================================================
// POST /contatos — Cadastra um novo paciente
// ============================================================
router.post('/', async (req, res) => {
  const {
    nome_completo, cpf, data_nascimento,
    whatsapp, telefone_fixo, email,
    logradouro, numero, complemento,
    bairro, cidade, estado, observacao
  } = req.body;

  if (!nome_completo || !whatsapp) {
    return res.status(400).json({
      sucesso: false,
      mensagem: 'Os campos "nome_completo" e "whatsapp" são obrigatórios.'
    });
  }

  try {
    const [resultado] = await db.execute(
      `INSERT INTO pacientes
        (nome_completo, cpf, data_nascimento, whatsapp, telefone_fixo, email,
         logradouro, numero, complemento, bairro, cidade, estado, observacao)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        nome_completo,
        cpf        || null,
        data_nascimento || null,
        whatsapp,
        telefone_fixo || null,
        email      || null,
        logradouro || null,
        numero     || null,
        complemento || null,
        bairro     || null,
        cidade     || null,
        estado     || null,
        observacao || null,
      ]
    );

    return res.status(201).json({
      sucesso: true,
      mensagem: 'Paciente cadastrado com sucesso.',
      id: resultado.insertId
    });
  } catch (erro) {
    console.error('Erro ao cadastrar paciente:', erro);
    return res.status(500).json({
      sucesso: false,
      mensagem: 'Erro interno ao cadastrar paciente.'
    });
  }
});

// ============================================================
// GET /contatos/buscar?nome=fred — Busca pacientes por nome
// ============================================================
router.get('/buscar', async (req, res) => {
  const { nome } = req.query;

  if (!nome || nome.trim().length < 2) {
    return res.status(400).json({
      sucesso: false,
      mensagem: 'Informe ao menos 2 caracteres para buscar.'
    });
  }

  try {
    const [pacientes] = await db.execute(
      'SELECT * FROM pacientes WHERE nome_completo LIKE ? ORDER BY nome_completo LIMIT 20',
      [`%${nome}%`]
    );

    return res.json({
      sucesso: true,
      total: pacientes.length,
      contatos: pacientes  // mantém chave "contatos" para o PHP não precisar mudar
    });
  } catch (erro) {
    console.error('Erro ao buscar pacientes:', erro);
    return res.status(500).json({
      sucesso: false,
      mensagem: 'Erro interno ao buscar pacientes.'
    });
  }
});

// ============================================================
// GET /contatos — Lista todos os pacientes (até 200)
// ============================================================
router.get('/', async (req, res) => {
  try {
    const [pacientes] = await db.execute(
      'SELECT * FROM pacientes ORDER BY nome_completo LIMIT 200',
      []
    );
    return res.json({ sucesso: true, total: pacientes.length, contatos: pacientes });
  } catch (erro) {
    console.error('Erro ao listar pacientes:', erro);
    return res.status(500).json({ sucesso: false, mensagem: 'Erro interno ao listar pacientes.' });
  }
});

// ============================================================
// GET /contatos/:id — Busca um paciente pelo ID
// ============================================================
router.get('/:id', async (req, res) => {
  const { id } = req.params;

  try {
    const [rows] = await db.execute(
      'SELECT * FROM pacientes WHERE id = ?',
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({
        sucesso: false,
        mensagem: 'Paciente não encontrado.'
      });
    }

    return res.json({ sucesso: true, contato: rows[0] });
  } catch (erro) {
    console.error('Erro ao buscar paciente:', erro);
    return res.status(500).json({
      sucesso: false,
      mensagem: 'Erro interno ao buscar paciente.'
    });
  }
});

// ============================================================
// PUT /contatos/:id — Atualiza os dados de um paciente
// ============================================================
router.put('/:id', async (req, res) => {
  const { id } = req.params;
  const {
    nome_completo, cpf, data_nascimento,
    whatsapp, telefone_fixo, email,
    logradouro, numero, complemento,
    bairro, cidade, estado, observacao
  } = req.body;

  if (!nome_completo || !whatsapp) {
    return res.status(400).json({
      sucesso: false,
      mensagem: 'Os campos "nome_completo" e "whatsapp" são obrigatórios.'
    });
  }

  try {
    const [resultado] = await db.execute(
      `UPDATE pacientes SET
        nome_completo=?, cpf=?, data_nascimento=?, whatsapp=?,
        telefone_fixo=?, email=?, logradouro=?, numero=?,
        complemento=?, bairro=?, cidade=?, estado=?, observacao=?
       WHERE id=?`,
      [
        nome_completo, cpf || null, data_nascimento || null, whatsapp,
        telefone_fixo || null, email || null, logradouro || null, numero || null,
        complemento || null, bairro || null, cidade || null, estado || null,
        observacao || null, id
      ]
    );

    if (resultado.affectedRows === 0) {
      return res.status(404).json({ sucesso: false, mensagem: 'Paciente não encontrado.' });
    }

    return res.json({ sucesso: true, mensagem: 'Paciente atualizado com sucesso.' });
  } catch (erro) {
    console.error('Erro ao atualizar paciente:', erro);
    return res.status(500).json({ sucesso: false, mensagem: 'Erro interno ao atualizar paciente.' });
  }
});

// ============================================================
// DELETE /contatos/:id — Remove um paciente pelo ID
// ============================================================
router.delete('/:id', async (req, res) => {
  const { id } = req.params;

  try {
    const [resultado] = await db.execute(
      'DELETE FROM pacientes WHERE id = ?',
      [id]
    );

    if (resultado.affectedRows === 0) {
      return res.status(404).json({ sucesso: false, mensagem: 'Paciente não encontrado.' });
    }

    return res.json({ sucesso: true, mensagem: 'Paciente excluído com sucesso.' });
  } catch (erro) {
    console.error('Erro ao excluir paciente:', erro);
    return res.status(500).json({ sucesso: false, mensagem: 'Erro interno ao excluir paciente.' });
  }
});

module.exports = router;
