<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$usuario = exigir_login();

$consulta = db()->prepare(
    'SELECT id, titulo, criado_em, atualizado_em FROM posts WHERE autor_id = ? ORDER BY criado_em DESC, id DESC'
);
$consulta->execute([$usuario['id']]);
$posts = $consulta->fetchAll();

$titulo = 'Meus posts';
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<section class="caixa">
    <div class="painel-topo">
        <div>
            <h1>Meus posts</h1>
            <p class="subtitulo">Logado como <?= e($usuario['nome']) ?> (<?= e($usuario['email']) ?>)</p>
        </div>
        <a class="botao" href="editor.php">+ Novo post</a>
    </div>

    <?php if (!$posts): ?>
        <p class="vazio-texto">Você ainda não escreveu nenhum post.</p>
    <?php else: ?>
        <ul class="lista-painel">
            <?php foreach ($posts as $post): ?>
                <li>
                    <div>
                        <a href="post.php?id=<?= $post['id'] ?>" class="painel-titulo"><?= e($post['titulo']) ?></a>
                        <p class="meta">
                            Publicado em <?= data_br($post['criado_em']) ?>
                            <?= $post['atualizado_em'] ? ' · editado em ' . data_br($post['atualizado_em']) : '' ?>
                        </p>
                    </div>
                    <div class="acoes">
                        <a class="botao botao-secundario botao-pequeno" href="editor.php?id=<?= $post['id'] ?>">Editar</a>
                        <form action="excluir.php" method="post" data-confirmar="Apagar &quot;<?= e($post['titulo']) ?>&quot;? Não dá para desfazer.">
                            <?= campo_csrf() ?>
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            <button type="submit" class="botao botao-perigo botao-pequeno">Apagar</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
