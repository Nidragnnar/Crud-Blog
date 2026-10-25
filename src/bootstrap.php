<?php
// Carregado no topo de todas as páginas: configuração, sessão, banco e funções.
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

const NOME_BLOG = 'Blog do Nicolas';

// Caminho do arquivo SQLite. Pode ser trocado pela variável de ambiente BLOG_DB.
define('CAMINHO_BANCO', getenv('BLOG_DB') ?: dirname(__DIR__) . '/dados/blog.sqlite');

// Cookie de sessão protegido: inacessível ao JavaScript e não enviado por outros sites
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

require __DIR__ . '/banco.php';
require __DIR__ . '/funcoes.php';
