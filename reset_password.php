<?php
require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';

$id = $_GET["id"] ?? $_POST["user_id"] ?? ''; //isset($_GET["id"]) ? $_GET["id"] : '';
$token = $_GET["token"] ?? $_POST["token"] ?? '';

if ($id == "" || $token == "") {
  header("Location: index.php");
  exit;
}

$db = new DataBase();

$con = $db->conectar();

$errors = [];

if (!verificaTokenRequest($id, $token, $con)) {
  echo "No se pudo verificar la información";
  exit;
}



if (!empty($_POST)) {


  $password = trim($_POST["password"]);
  $repassword = trim($_POST["repassword"]);

  if (esNulo([$id, $token, $password, $repassword])) {
    $errors[] = "Debe llenar todos los campos";
  }

  if (!validaPassword($password, $repassword)) {
    $errors[] = "Las contraseñas no coinciden";
  }


  if (count($errors) == 0) {

    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    if (actualizaPassword($id, $pass_hash, $con)) {
      echo "Contraseña modificada. <br> <a href='login.php'>Iniciar Sesión</a> ";
      exit;
    } else {
      $errors[] = "Error al modificar la contraseña inténtelo nuevamente";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tienda Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="js/all.js" crossorigin="anonymous"></script>
  <link href="css/estilos.css" rel="stylesheet">
</head>

<body>
<?php include 'menu.php' ?>

  <main class="form-login m-auto pt-4">
    <div class="container">

      <h2 class="text-center">Cambiar Contraseña</h2>

      <?php mostrarMensajes($errors) ?>

      <form action="reset_password.php" method="post" class="row g-3" autocomplete="off">

        <input type="hidden" name="user_id" id="user_id" value="<?php echo $id ?>">
        <input type="hidden" name="token" id="token" value="<?php echo $token ?>">

        <div class="form-floating">
          <input class="form-control" type="password" name="password" id="password" placeholder="Nueva Contraseña" required>
          <label for="password">Nueva Contraseña</label>
        </div>

        <div class="form-floating">
          <input class="form-control" type="password" name="repassword" id="repassword" placeholder="Repita Nueva Contraseña" required>
          <label for="repassword">Repita Nueva Contraseña</label>
        </div>

        <div class="d-grid gap-3 col-12">
          <button type="submit" class="btn btn-primary">Continuar</button>
        </div>

        <div class="col-12 mx-auto text-center">
          <a href="login.php">Iniciar Sesión</a>
        </div>


      </form>

    </div>
  </main>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



  <script>

  </script>

</body>

</html>