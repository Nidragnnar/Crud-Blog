<?php
// Espera a variável $titulo. Abre o HTML, o menu e mostra o aviso da sessão, se houver.
$usuario = usuario_atual();
$aviso = pegar_aviso();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | <?= e(NOME_BLOG) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>

<body>
    <canvas class="estrelas" aria-hidden="true"></canvas>

    <header class="cabecalho">
        <div class="container barra">
            <a href="index.php" class="logo">NICO<span>LAS</span></a>
            <nav class="menu" aria-label="Menu principal">
                <a href="index.php">Posts</a>
                <?php if ($usuario): ?>
                    <a href="painel.php">Meus posts</a>
                    <a href="editor.php" class="botao botao-pequeno">Novo post</a>
                    <form action="sair.php" method="post" class="form-sair">
                        <?= campo_csrf() ?>
                        <button type="submit" class="link">Sair</button>
                    </form>
                <?php else: ?>
                    <a href="entrar.php">Entrar</a>
                    <a href="cadastro.php" class="botao botao-pequeno">Criar conta</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container conteudo">
        <?php if ($aviso): ?>
            <p class="aviso aviso-<?= e($aviso['tipo']) ?>" role="status"><?= e($aviso['mensagem']) ?></p>
        <?php endif; ?>
