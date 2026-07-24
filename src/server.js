// ============================================================
// server.js — Ponto de entrada da aplicação Node.js
// ============================================================
// Este arquivo inicializa o servidor Express e o WhatsApp.
// É o equivalente ao "index.php" em um projeto PHP.
// ============================================================

require('dotenv').config(); // Carrega as variáveis de ambiente do arquivo .env

const express         = require('express');
const whatsappService = require('./services/whatsappService');
const verificarApiKey = require('./middlewares/autenticacao');

// Importa os grupos de rotas
const rotasStatus    = require('./routes/status');
const rotasContatos  = require('./routes/contatos');
const rotasMensagens = require('./routes/mensagens');

// Cria a aplicação Express
// Em PHP seria equivalente a criar o objeto da aplicação no Laravel
const app  = express();
const PORT = process.env.PORT || 3000;

// ============================================================
// Middlewares globais (executam em TODA requisição)
// ============================================================

// Permite que o Express leia o corpo (body) das requisições em formato JSON
// Em PHP isso acontece automaticamente via $_POST ou json_decode(file_get_contents('php://input'))
app.use(express.json());

// Permite que o PHP (e qualquer outro cliente) acesse esta API
// CORS = Cross-Origin Resource Sharing
app.use((req, res, next) => {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
  res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
  if (req.method === 'OPTIONS') return res.sendStatus(200);
  next();
});

// ============================================================
// Registro das rotas
// ============================================================
// O middleware "verificarApiKey" é aplicado antes de cada grupo,
// exigindo que todas as requisições tenham o header x-api-key.
// A rota /status fica fora para facilitar o monitoramento.
// ============================================================

app.use('/status',    rotasStatus);                          // sem autenticação
app.use('/contatos',  verificarApiKey, rotasContatos);       // protegida
app.use('/mensagens', verificarApiKey, rotasMensagens);      // protegida

// ============================================================
// Inicialização do servidor
// ============================================================
app.listen(PORT, async () => {
  console.log(`\n🚀 API rodando em http://localhost:${PORT}`);
  console.log('📡 Iniciando conexão com WhatsApp...\n');

  // Inicia a conexão com o WhatsApp em paralelo ao servidor HTTP
  // O servidor já responde enquanto aguarda o QR ser escaneado
  await whatsappService.iniciarConexao();
});
