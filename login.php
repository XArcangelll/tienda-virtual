<?php
require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';

$db = new DataBase();

$con = $db->conectar();

$proceso = isset($_GET["pago"]) ? 'pago' : 'login';

$errors = [];

if(isset($_SESSION["user_id"])){
  header("location: index.php");
}

if (!empty($_POST)) {

  $usuario = trim($_POST["usuario"]);
  $password = trim($_POST["password"]);
  $procesos = $_POST["proceso"] ?? 'login';

  if (esNulo([$usuario, $password])) {
    $errors[] = "Debe llenar todos los campos";
  }

  if (count($errors) == 0) {
    $errors[] =  login($usuario, $password, $con,$procesos);
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
  <link href="css/estilos.css" rel="stylesheet">
</head>

<body>
<?php include 'menu.php' ?>

  <main class="form-login m-auto pt-4">
    <h2 class="text-center">Iniciar Sesión</h2>

    <?php mostrarMensajes($errors) ?>

    <form class="row g-3" action="login.php" method="post" autocomplete="off">

    <input type="hidden" name="proceso" value="<?php echo $proceso?>">

      <div class="form-floating">
        <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Usuario" required>
        <label for="usuario">Usuario</label>
      </div>

      <div class="form-floating">
        <input class="form-control" type="password" name="password" id="password" placeholder="Contraseña" required>
        <label for="password">Contraseña</label>
      </div>

      <div class="col-12">
        <a href="recupera.php">¿Olvidaste tu contraseña?</a>
      </div>

      <div class="d-grid gap-3 col-12 ">
        <button type="submit" class="btn btn-primary">Ingresar</button>
      </div>

      <div class="col-12 mx-auto text-center">
        ¿No tiene cuenta? <a href="registro.php">Regístrese aquí</a>
      </div>


    </form>

  </main>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <script>


  </script>


</body>

</html>