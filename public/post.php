<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$consulta = db()->prepare(
    'SELECT p.*, u.nome AS autor
     FROM posts p JOIN usuarios u ON u.id = p.autor_id
     WHERE p.id = ?'
);
$consulta->execute([id_da_url()]);
$post = $consulta->fetch() ?: nao_encontrado();

$ehDono = usuario_atual() !== null && (int) usuario_atual()['id'] === (int) $post['autor_id'];

$titulo = $post['titulo'];
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<article class="caixa post">
    <a href="index.php" class="voltar">← Todos os posts</a>
    <h1><?= e($post['titulo']) ?></h1>
    <p class="meta">
        Por <?= e($post['autor']) ?> em <?= data_br($post['criado_em'], true) ?>
        <?php if ($post['atualizado_em']): ?>
            · editado em <?= data_br($post['atualizado_em'], true) ?>
        <?php endif; ?>
    </p>

    <div class="post-texto"><?= paragrafos($post['conteudo']) ?></div>

    <?php if ($ehDono): ?>
        <div class="acoes">
            <a class="botao botao-secundario" href="editor.php?id=<?= $post['id'] ?>">Editar</a>
            <form action="excluir.php" method="post" data-confirmar="Apagar este post? Não dá para desfazer.">
                <?= campo_csrf() ?>
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <button type="submit" class="botao botao-perigo">Apagar</button>
            </form>
        </div>
    <?php endif; ?>
</article>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
