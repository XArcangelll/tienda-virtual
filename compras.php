<?php
require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';

$db = new DataBase();

$con = $db->conectar();

$token = generarToken();

$_SESSION["token"] = $token;

$idCliente = $_SESSION["user_cliente"];
$sql = $con->prepare("SELECT id_transaccion, fecha, status, total, medio_pago FROM compra WHERE id_cliente = ? ORDER BY DATE(fecha) DESC");

    $sql->execute([$idCliente]);

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


    <main>
        <div class="container">

        <h4>Mis Compras</h4>
        <hr>

        <?php while($row = $sql->fetch(PDO::FETCH_ASSOC)){ ?>

        <div class="card mb-3">
  <div class="card-header">
        <?php echo $row["fecha"] ?>
  </div>
  <div class="card-body">
    <h5 class="card-title">Folio: <?php echo $row["id_transaccion"] ?> </h5>
    <p class="card-text">Total: <?php echo $row["total"] ?> </p>
    <a href="compra_detalle.php?orden=<?php echo $row["id_transaccion"] ?>&token=<?php echo $token ?>&id=<?php echo hash_hmac('sha1', $_SESSION["user_cliente"], KEY_TOKEN) ?>" class="btn btn-primary">Ver Compra</a>
  </div>
</div>
<?php }?>

        </div>
    </main>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>




</body>

</html>