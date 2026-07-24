# API WhatsApp — Laboratório

## Como rodar pela primeira vez

### 1. Banco de dados
```bash
mysql -u root -p < database/schema.sql
```

### 2. Configurar variáveis de ambiente
Edite o arquivo `.env` com seus dados reais:
- `DB_PASSWORD` — senha do MySQL
- `API_KEY` — troque por uma string aleatória longa

### 3. Instalar dependências Node.js
```bash
npm install
```

### 4. Iniciar a API
```bash
npm run dev   # modo desenvolvimento (reinicia ao salvar)
# ou
npm start     # modo produção
```

Na primeira execução, aparecerá um QR Code no terminal.
Escaneie com o WhatsApp Business do número de testes.

### 5. Frontend PHP
Coloque a pasta `frontend/` em seu servidor Apache/PHP e acesse `index.php`.
Certifique-se que `config.php` tem a mesma `API_KEY` do `.env`.

---

## Testar os endpoints com curl

### Verificar status do WhatsApp
```bash
curl http://localhost:3000/status
```

### Cadastrar um contato
```bash
curl -X POST http://localhost:3000/contatos \
  -H "Content-Type: application/json" \
  -H "x-api-key: troque_por_uma_chave_secreta_aleatoria" \
  -d '{"nome": "Fred Viana", "whatsapp": "5511999990001", "observacao": "Exame de sangue"}'
```

### Buscar contato por nome
```bash
curl "http://localhost:3000/contatos/buscar?nome=fred" \
  -H "x-api-key: troque_por_uma_chave_secreta_aleatoria"
```

### Enviar aviso de exame pronto
```bash
curl -X POST http://localhost:3000/mensagens/exame-pronto \
  -H "Content-Type: application/json" \
  -H "x-api-key: troque_por_uma_chave_secreta_aleatoria" \
  -d '{"contato_id": 1}'
```

### Enviar pesquisa de satisfação
```bash
curl -X POST http://localhost:3000/mensagens/pesquisa-satisfacao \
  -H "Content-Type: application/json" \
  -H "x-api-key: troque_por_uma_chave_secreta_aleatoria" \
  -d '{"contato_id": 1}'
```

---

## Estrutura de pastas

```
ApiWhatsapp/
├── src/
│   ├── server.js              # Ponto de entrada — inicializa Express e WhatsApp
│   ├── config/
│   │   └── database.js        # Pool de conexões MySQL
│   ├── middlewares/
│   │   └── autenticacao.js    # Verifica API Key em cada requisição
│   ├── routes/
│   │   ├── status.js          # GET /status
│   │   ├── contatos.js        # POST /contatos, GET /contatos/buscar
│   │   └── mensagens.js       # POST /mensagens/exame-pronto, /pesquisa-satisfacao
│   ├── services/
│   │   └── whatsappService.js # ÚNICA parte que conhece o Baileys — trocar aqui para migrar
│   └── templates/
│       └── mensagens.js       # Todos os textos de mensagem centralizados
├── auth/                      # Sessão do WhatsApp (gerado automaticamente, não commitar)
├── database/
│   └── schema.sql             # Cria as tabelas no MySQL
├── frontend/
│   ├── index.php              # Página principal do painel
│   ├── config.php             # URL da API e função chamarApi()
│   └── style.css              # Estilos
├── .env                       # Variáveis de ambiente (não commitar)
├── .gitignore
└── package.json
```
