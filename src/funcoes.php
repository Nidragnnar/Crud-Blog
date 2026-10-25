<?php
declare(strict_types=1);

// ========== Saída segura ==========
// Escapa texto antes de colocar no HTML (evita XSS). Use em TODO dado vindo do usuário.
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Mostra o texto de um post com parágrafos, sem permitir HTML
function paragrafos(string $texto): string
{
    $blocos = preg_split('/\R{2,}/', trim($texto));
    return implode('', array_map(fn($b) => '<p>' . nl2br(e($b)) . '</p>', $blocos));
}

function resumo(string $texto, int $limite = 180): string
{
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    return mb_strlen($texto) > $limite ? rtrim(mb_substr($texto, 0, $limite)) . '…' : $texto;
}

// Datas são guardadas em UTC pelo SQLite e mostradas no horário de Brasília
function data_br(string $utc, bool $comHora = false): string
{
    $data = new DateTime($utc, new DateTimeZone('UTC'));
    $data->setTimezone(new DateTimeZone(date_default_timezone_get()));
    return $data->format($comHora ? 'd/m/Y \à\s H:i' : 'd/m/Y');
}

// ========== Navegação ==========
function redirecionar(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function nao_encontrado(): never
{
    http_response_code(404);
    $titulo = 'Página não encontrada';
    require dirname(__DIR__) . '/views/cabecalho.php';
    echo '<section class="caixa centro"><h1>404</h1><p>Esta página não existe ou foi removida.</p>'
        . '<a class="botao" href="index.php">Voltar ao início</a></section>';
    require dirname(__DIR__) . '/views/rodape.php';
    exit;
}

function exigir_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }
}

// Mensagens que aparecem uma vez na próxima página ("Post publicado!")
function avisar(string $mensagem, string $tipo = 'sucesso'): void
{
    $_SESSION['aviso'] = ['mensagem' => $mensagem, 'tipo' => $tipo];
}

function pegar_aviso(): ?array
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}

// ========== Proteção CSRF ==========
// Cada formulário leva um token secreto da sessão; sem ele, o envio é recusado.
// Isso impede que outro site envie formulários em nome de quem está logado.
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verificar_csrf(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || !hash_equals(csrf_token(), $enviado)) {
        http_response_code(400);
        exit('Sessão expirada ou formulário inválido. Volte e tente de novo.');
    }
}

// ========== Login ==========
function usuario_atual(): ?array
{
    static $usuario = false;
    if ($usuario === false) {
        $usuario = null;
        if (isset($_SESSION['usuario_id'])) {
            $consulta = db()->prepare('SELECT id, nome, email FROM usuarios WHERE id = ?');
            $consulta->execute([$_SESSION['usuario_id']]);
            $usuario = $consulta->fetch() ?: null;
        }
    }
    return $usuario;
}

function exigir_login(): array
{
    $usuario = usuario_atual();
    if ($usuario === null) {
        avisar('Entre na sua conta para continuar.', 'erro');
        redirecionar('entrar.php');
    }
    return $usuario;
}

function entrar_como(int $usuarioId): void
{
    // Novo ID de sessão ao entrar: evita que alguém reaproveite uma sessão antiga
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuarioId;
}

// ========== Formulários ==========
// Lê um campo de texto do formulário; texto com codificação inválida (não UTF-8) é descartado
function texto_post(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) && mb_check_encoding($valor, 'UTF-8') ? trim($valor) : '';
}

function id_da_url(): int
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id ?: 0;
}

// Busca um post do usuário logado; se for de outra pessoa ou não existir, dá 404
function post_do_usuario(int $postId, int $usuarioId): array
{
    $consulta = db()->prepare('SELECT * FROM posts WHERE id = ? AND autor_id = ?');
    $consulta->execute([$postId, $usuarioId]);
    return $consulta->fetch() ?: nao_encontrado();
}
