// ============================================================
// server.js — Ponto de entrada da aplicação Node.js
// ============================================================

require('dotenv').config();

const express         = require('express');
const whatsappService = require('./services/whatsappService');
const verificarApiKey = require('./middlewares/autenticacao');

const rotasStatus    = require('./routes/status');
const rotasContatos  = require('./routes/contatos');
const rotasMensagens = require('./routes/mensagens');
const rotasWhatsapp  = require('./routes/whatsapp');

const app  = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

app.use((req, res, next) => {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
  res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
  if (req.method === 'OPTIONS') return res.sendStatus(200);
  next();
});

app.use('/status',    rotasStatus);
app.use('/contatos',  verificarApiKey, rotasContatos);
app.use('/mensagens', verificarApiKey, rotasMensagens);
app.use('/whatsapp',  verificarApiKey, rotasWhatsapp);

app.listen(PORT, async () => {
  console.log(`\n🚀 API rodando em http://localhost:${PORT}`);
  console.log('📡 Iniciando conexões WhatsApp 1 e 2...\n');
  await whatsappService.iniciarTodasConexoes();
});
