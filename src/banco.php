<?php
declare(strict_types=1);

// Conexão única com o SQLite. As tabelas são criadas automaticamente na primeira vez.
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $pasta = dirname(CAMINHO_BANCO);
    if (!is_dir($pasta)) {
        mkdir($pasta, 0775, true);
    }

    $pdo = new PDO('sqlite:' . CAMINHO_BANCO, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    criarTabelas($pdo);

    return $pdo;
}

function criarTabelas(PDO $pdo): void
{
    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS usuarios (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            nome       TEXT    NOT NULL,
            email      TEXT    NOT NULL UNIQUE COLLATE NOCASE,
            senha_hash TEXT    NOT NULL,
            criado_em  TEXT    NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS posts (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            autor_id      INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
            titulo        TEXT    NOT NULL,
            conteudo      TEXT    NOT NULL,
            criado_em     TEXT    NOT NULL DEFAULT (datetime('now')),
            atualizado_em TEXT
        );

        CREATE INDEX IF NOT EXISTS idx_posts_autor ON posts(autor_id);
        SQL);
}
