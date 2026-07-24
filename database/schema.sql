-- ============================================================
-- schema.sql — Estrutura do banco de dados
-- ============================================================
-- Execute este arquivo no MySQL/MariaDB para criar as tabelas.
-- Comando: mysql -u root -p < database/schema.sql
-- ============================================================

-- Cria o banco se não existir
CREATE DATABASE IF NOT EXISTS laboratorio_whatsapp
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE laboratorio_whatsapp;

-- ============================================================
-- Tabela de contatos
-- ============================================================
CREATE TABLE IF NOT EXISTS contatos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(150) NOT NULL,
  whatsapp   VARCHAR(20)  NOT NULL,        -- formato: 5511999999999
  observacao VARCHAR(255) NULL,            -- campo livre para anotações
  criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índice para acelerar buscas por nome
CREATE INDEX idx_contatos_nome ON contatos (nome);

-- ============================================================
-- Tabela de log de envios (histórico de mensagens enviadas)
-- ============================================================
CREATE TABLE IF NOT EXISTS log_envios (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  contato_id  INT          NOT NULL,
  tipo        VARCHAR(50)  NOT NULL,   -- ex: 'exame_pronto', 'pesquisa_satisfacao'
  mensagem    TEXT         NOT NULL,   -- texto completo enviado
  status      VARCHAR(20)  NOT NULL DEFAULT 'enviado',  -- 'enviado' ou 'erro'
  erro        TEXT         NULL,       -- detalhes do erro, se houver
  enviado_em  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (contato_id) REFERENCES contatos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- FUTURO: Tabela de fila de envio (para alto volume)
-- ============================================================
-- Quando o volume crescer, em vez de enviar direto, a API
-- insere aqui e um "worker" (processo separado) processa a fila,
-- respeitando limites de envios por minuto.
-- ============================================================
CREATE TABLE IF NOT EXISTS fila_envio (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  contato_id     INT         NOT NULL,
  tipo           VARCHAR(50) NOT NULL,
  agendado_para  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tentativas     INT         NOT NULL DEFAULT 0,
  status         VARCHAR(20) NOT NULL DEFAULT 'pendente',  -- 'pendente', 'processando', 'enviado', 'erro'
  criado_em      DATETIME    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (contato_id) REFERENCES contatos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Dados de exemplo para teste
-- ============================================================
INSERT INTO contatos (nome, whatsapp, observacao) VALUES
  ('Fred Viana', '5511999990001', 'Exame de sangue'),
  ('Maria Silva', '5511999990002', 'Exame de urina'),
  ('João Santos', '5511999990003', NULL);
