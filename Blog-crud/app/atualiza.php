<?php
require 'conexao.php';
$id = $_POST['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$idade = $_POST['idade'];
$telefone = $_POST['telefone'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$query = "UPDATE usuarios SET nome = '$nome', email = '$email',idade = $idade,telefone = '$telefone',senha = '$senha' WHERE id = $id ";

$exec = mysqli_query($conexao, $query);

header('Location: lista-usu.php');



?>