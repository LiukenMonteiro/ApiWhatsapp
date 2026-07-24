# API WhatsApp — Laboratório

Sistema de envio automático de mensagens WhatsApp para laboratório de exames.  
Substitui serviços pagos como o Worklab (~R$ 150/mês).

---

## O que o sistema faz

- Conecta ao **WhatsApp Business** via QR Code
- Cadastra pacientes com nome, CPF, endereço, WhatsApp etc.
- Envia mensagens automáticas: **exame pronto** e **pesquisa de satisfação**
- Painel web em PHP para gerenciar tudo sem precisar de terminal

---

## Forma recomendada: Docker (Windows, Linux e Mac)

> Não precisa instalar Node.js, PHP nem SQLite na sua máquina.  
> Basta ter o **Docker Desktop** instalado.

### 1. Instale o Docker Desktop

- **Windows**: https://www.docker.com/products/docker-desktop  
- **Linux (Ubuntu)**: `sudo apt install docker.io docker-compose-v2 -y`
- **Mac**: https://www.docker.com/products/docker-desktop

### 2. Clone o projeto

```bash
git clone https://github.com/LiukenMonteiro/ApiWhatsapp.git
cd ApiWhatsapp
```

### 3. Configure o arquivo `.env`

Copie o arquivo de exemplo e edite com seus dados:

```bash
# Linux / Mac
cp .env.example .env

# Windows (Prompt de Comando)
copy .env.example .env
```

Abra o `.env` e preencha:

```env
PORT=3000
WHATSAPP_NUMERO=5511999990001   # número do WhatsApp Business (só dígitos)
API_KEY=coloque_uma_chave_secreta_aqui
```

> **Importante:** a `API_KEY` pode ser qualquer texto longo e aleatório.  
> Ela protege os endpoints da API contra acesso não autorizado.

### 4. Suba os containers

```bash
docker compose up --build
```

Na primeira vez leva alguns minutos para baixar as imagens. Nas próximas vezes é rápido.

### 5. Conecte o WhatsApp

Abra outro terminal e veja os logs da API para encontrar o QR Code:

```bash
docker logs -f whatsapp_api
```

Um QR Code aparecerá no terminal. Escaneie com o **WhatsApp Business**:

1. Abra o WhatsApp Business no celular
2. Toque em **⋮ Menu → Aparelhos conectados**
3. Toque em **Conectar um aparelho**
4. Aponte a câmera para o QR Code

Você verá a mensagem `✅ WhatsApp conectado!`.

### 6. Acesse o painel

Abra o navegador em: **http://localhost:8080**

---

## Sem Docker (desenvolvimento local)

Requisitos:
- **Node.js 22** (https://nodejs.org)
- **PHP 8.2+** com extensão cURL

### 1. Instale as dependências

```bash
npm install
```

### 2. Configure o `.env`

```bash
cp .env.example .env
# edite o .env com seus dados
```

### 3. Inicie a API

```bash
npm run dev
```

Escaneie o QR Code que aparecerá no terminal.

### 4. Inicie o frontend PHP

Em outro terminal:

```bash
php -S localhost:8080 -t frontend/
```

Acesse: **http://localhost:8080**

---

## Estrutura do projeto

```
ApiWhatsapp/
├── src/
│   ├── server.js              # Ponto de entrada da API
│   ├── config/
│   │   └── database.js        # Conexão SQLite
│   ├── middlewares/
│   │   └── autenticacao.js    # Validação da API Key
│   ├── routes/
│   │   ├── contatos.js        # CRUD de pacientes
│   │   ├── mensagens.js       # Envio de mensagens
│   │   └── status.js          # Status da conexão
│   ├── services/
│   │   └── whatsappService.js # Conexão com WhatsApp via Baileys
│   └── templates/
│       └── mensagens.js       # Textos das mensagens
├── frontend/
│   ├── index.php              # Painel administrativo
│   ├── config.php             # Configuração do frontend
│   └── style.css              # Estilos
├── database/
│   └── schema.sql             # Schema do banco (referência)
├── docker-compose.yml
├── Dockerfile
└── .env.example
```

---

## Endpoints da API

Todos os endpoints (exceto `/status`) exigem o header `x-api-key`.

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/status` | Status da conexão WhatsApp |
| GET | `/contatos` | Lista todos os pacientes |
| POST | `/contatos` | Cadastra novo paciente |
| GET | `/contatos/buscar?nome=X` | Busca pacientes por nome |
| GET | `/contatos/:id` | Busca paciente por ID |
| PUT | `/contatos/:id` | Atualiza dados do paciente |
| DELETE | `/contatos/:id` | Remove paciente |
| POST | `/mensagens/exame-pronto` | Envia aviso de exame pronto |
| POST | `/mensagens/pesquisa-satisfacao` | Envia pesquisa de satisfação |

---

## Problemas comuns

**QR Code não aparece / erro de conexão**  
O sistema busca automaticamente a versão mais recente do WhatsApp Web.  
Se o erro persistir, delete a pasta `auth/` e reinicie.

**WhatsApp desconectou**  
A reconexão é automática (até 5 tentativas). Se falhar, reinicie o container:
```bash
docker compose restart api
```

**Sessão expirou (logout)**  
Delete a pasta `auth/` e escaneie o QR Code novamente:
```bash
rm -rf auth/
docker compose restart api
```
