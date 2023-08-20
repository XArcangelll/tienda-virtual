<?php
require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';

if(isset($_SESSION["user_id"])){
  header("location: index.php");
}

$db = new DataBase();

$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {

  $email = trim($_POST["email"]);

  if (esNulo([$email])) {
    $errors[] = "Debe llenar todos los campos";
  }

  if (!esEmail($email)) {
    $errors[] = "La dirección de correo no es válida";
  }

  if (count($errors) == 0) {
    if (emailExiste($email, $con)) {
      $sql = $con->prepare("SELECT usuarios.id, clientes.nombres FROM usuarios INNER JOIN clientes ON usuarios.id_cliente = clientes.id WHERE clientes.email LIKE ? LIMIT 1");
      $sql->execute([$email]);
      $row = $sql->fetch(PDO::FETCH_ASSOC);
      $user_id = $row["id"];
      $nombres = $row["nombres"];

      $token = solicitaPassword($user_id, $con);

      if ($token !== null) {
        require 'clases/Mailer.php';
        $mailer = new Mailer();

        $url = SITE_URL . '/reset_password.php?id=' . $user_id . '&token=' . $token;

        $asunto = "Recuperar password - Tienda Online";

        $cuerpo = "Estimado $nombres: <br> Si has solicitado el cambio de tu contraseña da clic en el 
                siguiente link <a href='$url'>Reestablecer Contraseña</a>";
        $cuerpo .= "<br>Si no hiciste esta solicitud puedes ignorar este correo.";

        if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
          echo "<p><b>Correo enviado</b></p>";
          echo "<p>Hemos enviado enviado un correo electrónico a la dirección $email para reestablecer la contraseña</p>";
          exit;
        }
      } else {
        $errors[] = "No se pudo procesar el cambio de contraseña";
      }
    } else {
      $errors[] = "No existe una cuenta asociada a esta dirección de correo";
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

      <h2 class="text-center mb-3">Recuperar Contraseña</h2>

      <?php mostrarMensajes($errors) ?>

      <form action="recupera.php" method="post" class="row g-3" autocomplete="off">

        <div class="form-floating">
          <input class="form-control" type="email" name="email" id="email" placeholder="Email" required>
          <label for="email">Correo electrónico</label>
        </div>

        <div class="d-grid gap-3 col-12">
          <button type="submit" class="btn btn-primary">Solicitar</button>
        </div>

        <div class="col-12 mx-auto text-center">
          ¿No tiene cuenta? <a href="registro.php">Regístrese aquí</a>
        </div>


      </form>

    </div>
  </main>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



  <script>

  </script>

</body>

</html>