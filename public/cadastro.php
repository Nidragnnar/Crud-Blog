<?php
require dirname(__DIR__) . '/src/bootstrap.php';

if (usuario_atual()) {
    redirecionar('painel.php');
}

$erros = [];
$dados = ['nome' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();

    $dados['nome'] = texto_post('nome');
    $dados['email'] = mb_strtolower(texto_post('email'));
    $senha = $_POST['senha'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';

    if (mb_strlen($dados['nome']) < 2 || mb_strlen($dados['nome']) > 80) {
        $erros['nome'] = 'O nome deve ter entre 2 e 80 caracteres.';
    }
    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Digite um e-mail válido.';
    }
    if (!is_string($senha) || mb_strlen($senha) < 8) {
        $erros['senha'] = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmacao) {
        $erros['confirmacao'] = 'As senhas não são iguais.';
    }

    if (!isset($erros['email'])) {
        $existe = db()->prepare('SELECT 1 FROM usuarios WHERE email = ?');
        $existe->execute([$dados['email']]);
        if ($existe->fetchColumn()) {
            $erros['email'] = 'Já existe uma conta com este e-mail.';
        }
    }

    if (!$erros) {
        $inserir = db()->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
        $inserir->execute([$dados['nome'], $dados['email'], password_hash($senha, PASSWORD_DEFAULT)]);

        entrar_como((int) db()->lastInsertId());
        avisar('Conta criada! Bem-vindo(a), ' . $dados['nome'] . '.');
        redirecionar('painel.php');
    }
}

$titulo = 'Criar conta';
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<section class="caixa formulario-caixa">
    <h1>Criar conta</h1>
    <p class="subtitulo">Crie sua conta para escrever posts.</p>

    <form method="post" novalidate>
        <?= campo_csrf() ?>

        <div class="campo<?= isset($erros['nome']) ? ' invalido' : '' ?>">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" maxlength="80" autocomplete="name" required
                value="<?= e($dados['nome']) ?>">
            <small class="erro"><?= e($erros['nome'] ?? '') ?></small>
        </div>

        <div class="campo<?= isset($erros['email']) ? ' invalido' : '' ?>">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" autocomplete="email" required
                value="<?= e($dados['email']) ?>">
            <small class="erro"><?= e($erros['email'] ?? '') ?></small>
        </div>

        <div class="campo<?= isset($erros['senha']) ? ' invalido' : '' ?>">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" minlength="8" autocomplete="new-password" required>
            <small class="erro"><?= e($erros['senha'] ?? '') ?></small>
        </div>

        <div class="campo<?= isset($erros['confirmacao']) ? ' invalido' : '' ?>">
            <label for="confirmacao">Confirme a senha</label>
            <input id="confirmacao" name="confirmacao" type="password" autocomplete="new-password" required>
            <small class="erro"><?= e($erros['confirmacao'] ?? '') ?></small>
        </div>

        <button type="submit" class="botao botao-largo">Criar conta</button>
    </form>

    <p class="troca">Já tem conta? <a href="entrar.php">Entrar</a></p>
</section>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
