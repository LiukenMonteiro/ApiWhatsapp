// ============================================================
// middlewares/autenticacao.js — Middleware de API Key
// ============================================================
// "Middleware" em Express é uma função que fica entre a
// requisição e a resposta — como um "filtro" ou "interceptor".
// Este middleware verifica se a requisição veio com a API Key
// correta no cabeçalho (header) HTTP.
//
// Em PHP seria equivalente a um bloco no início de cada arquivo
// que verifica $_SERVER['HTTP_X_API_KEY'] antes de continuar.
//
// FUTURO MULTI-TENANT: aqui você buscará a api_key no banco
// e identificará qual laboratório está fazendo a requisição,
// carregando a sessão WhatsApp correspondente.
// ============================================================

require('dotenv').config();

function verificarApiKey(req, res, next) {
  // Lê o cabeçalho "x-api-key" enviado pelo cliente
  const chaveEnviada = req.headers['x-api-key'];

  if (!chaveEnviada || chaveEnviada !== process.env.API_KEY) {
    return res.status(401).json({
      sucesso: false,
      mensagem: 'Acesso negado. API Key inválida ou ausente.'
    });
  }

  // "next()" passa para o próximo passo — a rota em si
  // É como um "continue" no fluxo de execução
  next();
}

module.exports = verificarApiKey;
