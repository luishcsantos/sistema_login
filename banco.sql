-- =====================================================
-- banco.sql - Banco de dados do Mural de Avisos
-- Execute este script inteiro no MySQL Workbench
-- Atenção: ele apaga as tabelas antigas e recria tudo do zero
-- =====================================================

-- Cria o banco de dados, se ele ainda não existir
CREATE DATABASE IF NOT EXISTS sistema_login CHARACTER SET utf8mb4;

-- Seleciona o banco de dados que será usado
USE sistema_login;

-- Apaga as tabelas antigas (uma não depende da outra, a ordem não importa)
DROP TABLE IF EXISTS avisos;
DROP TABLE IF EXISTS usuarios;

-- Cria a tabela de usuários
-- O campo perfil guarda 'admin' (pode tudo) ou 'comum' (só lê os avisos)
CREATE TABLE usuarios (
    id     INT AUTO_INCREMENT PRIMARY KEY,   -- número único do usuário
    nome   VARCHAR(100) NOT NULL,            -- nome do usuário
    email  VARCHAR(150) NOT NULL UNIQUE,     -- e-mail usado para entrar (não repete)
    senha  VARCHAR(255) NOT NULL,            -- hash da senha (nunca a senha original)
    perfil VARCHAR(10)  NOT NULL             -- 'admin' ou 'comum'
);

-- Cria a tabela de avisos (o mural)
-- O autor é guardado só como texto, sem ligação com a tabela de usuários
CREATE TABLE avisos (
    id        INT AUTO_INCREMENT PRIMARY KEY,                -- número único do aviso
    titulo    VARCHAR(150) NOT NULL,                         -- título do aviso
    mensagem  TEXT NOT NULL,                                 -- texto do aviso
    autor     VARCHAR(100) NOT NULL,                         -- nome de quem publicou
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP   -- data e hora da publicação
);
