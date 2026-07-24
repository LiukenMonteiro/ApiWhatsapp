// ============================================================
// database.js — Conexão com SQLite (banco de dados em arquivo)
// ============================================================
// SQLite é diferente do MySQL: não é um servidor separado.
// O banco inteiro fica em um único arquivo "laboratorio.db"
// na pasta do projeto. Sem instalar nada, sem senha, sem porta.
// Ideal para desenvolvimento e projetos de pequeno/médio porte.
//
// "better-sqlite3" é uma biblioteca Node.js que inclui o
// SQLite embutido — nenhuma instalação extra necessária.
//
// FUTURO: para migrar para MySQL basta trocar este arquivo.
// A interface (função "executar") permanece a mesma.
// ============================================================

const Database = require('better-sqlite3');
const path     = require('path');

// Abre (ou cria) o arquivo do banco na raiz do projeto
const db = new Database(path.join(__dirname, '../../laboratorio.db'));

// Melhora performance: agrupa escritas em disco
db.pragma('journal_mode = WAL');

// ============================================================
// Cria as tabelas automaticamente se não existirem
// ============================================================
db.exec(`
  CREATE TABLE IF NOT EXISTS pacientes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_completo   TEXT    NOT NULL,
    cpf             TEXT,
    data_nascimento TEXT,
    whatsapp        TEXT    NOT NULL,
    telefone_fixo   TEXT,
    email           TEXT,
    logradouro      TEXT,
    numero          TEXT,
    complemento     TEXT,
    bairro          TEXT,
    cidade          TEXT,
    estado          TEXT,
    observacao      TEXT,
    criado_em       TEXT    DEFAULT (datetime('now','localtime'))
  );

  CREATE TABLE IF NOT EXISTS log_envios (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente_id INTEGER NOT NULL,
    tipo        TEXT    NOT NULL,
    mensagem    TEXT    NOT NULL,
    status      TEXT    NOT NULL DEFAULT 'enviado',
    erro        TEXT,
    enviado_em  TEXT    DEFAULT (datetime('now','localtime')),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
  );
`);

// ============================================================
// Adaptador: interface compatível com o resto do código
// ============================================================
// O código das rotas usa "await db.execute(sql, params)".
// Esta função traduz isso para as chamadas do better-sqlite3,
// que é síncrono (não usa async/await internamente).
// Retorna no mesmo formato do mysql2: [linhas] ou [resultado].
// ============================================================
const pool = {
  execute: (sql, params = []) => {
    const stmt       = db.prepare(sql);
    const sqlUpper   = sql.trim().toUpperCase();

    if (sqlUpper.startsWith('SELECT')) {
      // SELECT: retorna array de linhas
      return Promise.resolve([stmt.all(params)]);
    } else {
      // INSERT/UPDATE/DELETE: retorna objeto com insertId
      const resultado = stmt.run(params);
      return Promise.resolve([{
        insertId:     resultado.lastInsertRowid,
        affectedRows: resultado.changes,
      }]);
    }
  }
};

module.exports = pool;
