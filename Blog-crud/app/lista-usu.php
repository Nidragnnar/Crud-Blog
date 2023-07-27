<?php
require 'conexao.php';
$query = "SELECT *  FROM usuarios";
$data = mysqli_query($conexao, $query);
?>


<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lista de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <style>
  input{
    width: 80%;
  }
  </style>


</head>

<body>

  <nav class="navbar bg-dark border-bottom border-bottom-dark p-3" data-bs-theme="dark">
    <h1 class="text-light">Lista de Usuários</h1>
    <a class="nav-link text-light fs-3" href="index.html">Home</a>
  </nav>
  <div class="container mt-3">
    <table class="table  table-hover table-borderless">
      <thead class="table-dark">
        <tr class="aligh-midlle text-center">
          <th scope="col">#</th>
          <th scope="col">Nome</th>
          <th scope="col">E-mail</th>
          <th scope="col">Senha</th>
          <th scope="col">Telefone</th>
          <th scope="col">Idade</th>
          <th scope="col" colspan="2">Ações</th>
          <th></th>

        </tr>
      </thead>
      <tbody>
        <?php
        while ($usuario = mysqli_fetch_array($data)) {


        ?>
          <tr class="align-middle">
            <form action="atualiza.php" method="post">
              <th scope="row"><input type="hidden" name="id" value="<?php echo $usuario['id'] ?>"><?php echo $usuario['id'] ?></th>
              <td><input type="text" name="nome" value="<?php echo $usuario['nome'] ?>"></td>
              <td><input type="email" name="email" id="email" value="<?php echo $usuario['email'] ?>"></td>
              <td class="senha"><input type="text" name="senha" value="<?php echo $usuario['senha'] ?>"></td>
              <td><input type="number" name="idade" id="idade" value="<?php echo $usuario['idade'] ?>"></td>
              <td><input type="text" name="telefone" id="telefone" value="<?php echo $usuario['telefone'] ?>"></td>
              <td>
                <button type="submit" class="btn btn-primary p-2 d-inline-flex align-items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> Editar</button>

              </td>
            </form>
            <td>
              <form action="deleta.php" method="post">
                <input type="hidden" name="id" value="<?php echo $usuario['id'] ?>">
                <button type="submit" class="btn btn-danger p-2 d-inline-flex  align-items-center gap-2"><i class="fa-solid fa-trash"></i> Deletar</button>
              </form>
            </td>
          </tr>

        <?php
        }
        ?>


      </tbody>
    </table>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>