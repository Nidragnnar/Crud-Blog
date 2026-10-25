<?php
require dirname(__DIR__) . '/src/bootstrap.php';

if (usuario_atual()) {
    redirecionar('painel.php');
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();

    $email = mb_strtolower(texto_post('email'));
    $senha = $_POST['senha'] ?? '';

    $consulta = db()->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ?');
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();

    // Mesma mensagem para e-mail ou senha errados, para não revelar quais e-mails existem
    if ($usuario && is_string($senha) && password_verify($senha, $usuario['senha_hash'])) {
        entrar_como((int) $usuario['id']);
        avisar('Olá, ' . $usuario['nome'] . '!');
        redirecionar('painel.php');
    }
    $erro = 'E-mail ou senha incorretos.';
}

$titulo = 'Entrar';
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<section class="caixa formulario-caixa">
    <h1>Entrar</h1>
    <p class="subtitulo">Acesse sua conta para gerenciar seus posts.</p>

    <?php if ($erro): ?>
        <p class="aviso aviso-erro" role="alert"><?= e($erro) ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <?= campo_csrf() ?>

        <div class="campo">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>">
        </div>

        <div class="campo">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" autocomplete="current-password" required>
        </div>

        <button type="submit" class="botao botao-largo">Entrar</button>
    </form>

    <p class="troca">Ainda não tem conta? <a href="cadastro.php">Criar conta</a></p>
</section>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
