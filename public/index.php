<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$posts = db()->query(
    'SELECT p.id, p.titulo, p.conteudo, p.criado_em, u.nome AS autor
     FROM posts p JOIN usuarios u ON u.id = p.autor_id
     ORDER BY p.criado_em DESC, p.id DESC'
)->fetchAll();

$titulo = 'Início';
require dirname(__DIR__) . '/views/cabecalho.php';
?>

<section class="banner">
    <h1><?= e(NOME_BLOG) ?></h1>
    <p>Ideias, estudos e curiosidades sobre tecnologia e o espaço.</p>
</section>

<?php if (!$posts): ?>
    <section class="caixa centro vazio">
        <h2>Nenhum post ainda</h2>
        <p>Que tal escrever o primeiro?</p>
        <a class="botao" href="<?= usuario_atual() ? 'editor.php' : 'cadastro.php' ?>">Escrever um post</a>
    </section>
<?php else: ?>
    <section class="lista-posts" aria-label="Posts">
        <?php foreach ($posts as $post): ?>
            <article class="card-post">
                <a href="post.php?id=<?= $post['id'] ?>">
                    <p class="meta"><?= e($post['autor']) ?> · <?= data_br($post['criado_em']) ?></p>
                    <h2><?= e($post['titulo']) ?></h2>
                    <p><?= e(resumo($post['conteudo'])) ?></p>
                    <span class="ler-mais">Ler post →</span>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require dirname(__DIR__) . '/views/rodape.php'; ?>
