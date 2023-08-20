<?php


require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';


$id = isset($_GET["id"]) ? $_GET["id"] : "";
$token = isset($_GET["token"]) ? $_GET["token"] : "";

if ($id == "" || $token == "") {
  header("Location: index.php");
  exit;
}

$db = new DataBase();
$con = $db->conectar();

$mensaje = validaToken($id, $token, $con);

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

  <main>
    <div class="container">
      <div class="alert alert-<?php echo $mensaje["ok"] ?>" role="alert">
        <?php echo $mensaje["mensaje"] ?>
      </div>

    </div>
    </div>
  </main>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



</body>

</html>