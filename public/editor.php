<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$usuario = exigir_login();

// Sem ?id= cria um post novo; com ?id= edita (só se o post for do usuário logado)
$id = id_da_url();
$post = $id ? post_do_usuario($id, (int) $usuario['id']) : ['titulo' => '', 'conteudo' => ''];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();

    $post['titulo'] = texto_post('titulo');
    $post['conteudo'] = texto_post('conteudo');

    if (mb_strlen($post['titulo']) < 3 || mb_strlen($post['titulo']) > 150) {
        $erros['titulo'] = 'O título deve ter entre 3 e 150 caracteres.';
    }
    if (mb_strlen($post['conteudo']) < 10 || mb_strlen($post['conteudo']) > 20000) {
        $erros['conteudo'] = 'O texto deve ter entre 10 e 20.000 caracteres.';
    }

    if (!$erros) {
        if ($id) {
            $salvar = db()->prepare(
                "UPDATE posts SET titulo = ?, conteudo = ?, atualizado_em = datetime('now') WHERE id = ? AND autor_id = ?"
            );
            $salvar->execute([$post['titulo'], $post['conteudo'], $id, $usuario['id']]);
            avisar('Post atualizado!');
        } else {
            $salvar = db()->prepare('INSERT INTO posts (autor_id, titulo, conteudo) VALUES (?, ?, ?)');
            $salvar->execute([$usuario['id'], $post['titulo'], $post['conteudo']]);
            $id = (int) db()->lastInsertId();
            avisar('Post publicado!');
        }
        redirecionar('post.php?id=' . $id);
    }
}

$titulo = $id ? 'Editar post' : 'Novo post';
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<section class="caixa">
    <a href="painel.php" class="voltar">← Meus posts</a>
    <h1><?= e($titulo) ?></h1>

    <form method="post" class="editor" novalidate>
        <?= campo_csrf() ?>

        <div class="campo<?= isset($erros['titulo']) ? ' invalido' : '' ?>">
            <label for="titulo">Título</label>
            <input id="titulo" name="titulo" type="text" maxlength="150" required value="<?= e($post['titulo']) ?>">
            <small class="erro"><?= e($erros['titulo'] ?? '') ?></small>
        </div>

        <div class="campo<?= isset($erros['conteudo']) ? ' invalido' : '' ?>">
            <label for="conteudo">Texto</label>
            <textarea id="conteudo" name="conteudo" rows="14" maxlength="20000" required><?= e($post['conteudo']) ?></textarea>
            <small class="dica">Deixe uma linha em branco para começar um novo parágrafo.</small>
            <small class="erro"><?= e($erros['conteudo'] ?? '') ?></small>
        </div>

        <div class="acoes">
            <button type="submit" class="botao"><?= $id ? 'Salvar alterações' : 'Publicar' ?></button>
            <a class="botao botao-secundario" href="<?= $id ? 'post.php?id=' . $id : 'painel.php' ?>">Cancelar</a>
        </div>
    </form>
</section>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
