<?php
require dirname(__DIR__) . '/src/bootstrap.php';

// Sair só por POST com token, para um link malicioso não deslogar ninguém
exigir_post();
verificar_csrf();

$_SESSION = [];
session_regenerate_id(true);
avisar('Você saiu da sua conta.');
redirecionar('index.php');
