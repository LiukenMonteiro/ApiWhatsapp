FROM node:22-alpine

WORKDIR /app

# Instala dependências primeiro (aproveita cache do Docker)
COPY package*.json ./
RUN npm ci --only=production

# Copia o código-fonte
COPY src/ ./src/

EXPOSE 3000

CMD ["node", "src/server.js"]
