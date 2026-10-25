<?php
require dirname(__DIR__) . '/src/bootstrap.php';

// Apagar só por POST com token e apenas posts do próprio usuário
exigir_post();
verificar_csrf();
$usuario = exigir_login();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
post_do_usuario($id, (int) $usuario['id']);

$apagar = db()->prepare('DELETE FROM posts WHERE id = ? AND autor_id = ?');
$apagar->execute([$id, $usuario['id']]);

avisar('Post apagado.');
redirecionar('painel.php');
