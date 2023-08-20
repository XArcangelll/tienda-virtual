<?php
require "config/config.php";
require "config/database.php";
require 'clases/clienteFunciones.php';

$token_session = $_SESSION["token"];
$orden = $_GET["orden"] ?? null;
$token = $_GET["token"] ?? null;
$id = $_GET["id"] ?? null;
$tokenidtemporal = hash_hmac('sha1', $_SESSION["user_cliente"], KEY_TOKEN);

if($orden == null || $token == null || $id == null || $token != $token_session || $id != $tokenidtemporal ){
    header("Location: compras.php");
    exit;
} 



$db = new DataBase();

$con = $db->conectar();

$sqlCompra = $con->prepare("SELECT id , id_transaccion, fecha,total FROM compra WHERE id_transaccion = ? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idcompra = $rowCompra["id"];

$fecha = new DateTime($rowCompra["fecha"]);
$fecha = $fecha->format('d-m-Y H:i');

$sqlDetalle = $con->prepare("SELECT id,nombre,precio,cantidad FROM detalle_compra WHERE id_compra = ?");
$sqlDetalle->execute([$idcompra]);

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

            <div class="row">
                <div class="col-12 col-md-4">
                        <div class="card mb-3">
                                    <div class="card-header">
                                        <strong>Detalle de la Compra</strong>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Fecha: </strong> <?php echo $fecha; ?></p>
                                        <p><strong>Orden: </strong> <?php echo $rowCompra["id_transaccion"] ?></p>
                                        <p><strong>Total: </strong> <?php echo MONEDA. number_format($rowCompra["total"],2,',','.'); ?></p>
                                    </div>
                        </div>  
                </div>

                <div class="col-12 col-md-8">
                        <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)){
                                                $precio = $row["precio"];
                                                $cantidad = $row["cantidad"];
                                                $subtotal = $row["precio"] * $row["cantidad"];
                                            ?>
                                            <tr>
                                                <td><?php echo $row["nombre"] ?></td>
                                                <td><?php echo MONEDA. number_format($precio,2,',','.'); ?></td>
                                                <td><?php echo $cantidad ?></td>
                                                <td><?php echo  MONEDA. number_format($subtotal,2,',','.'); ?></td>
                                            </tr>

                                            <?php }?>
                                    </tbody>
                                </table>
                        </div>
                </div>
            </div>

        </div>
    </main>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>




</body>

</html>