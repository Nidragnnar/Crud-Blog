<?php  
require  'conexao.php';

$id = $_POST['id'];

$query ="DELETE FROM usuarios WHERE id = $id";

mysqli_query($conexao, $query);

header('Location:lista-usu.php');

?>