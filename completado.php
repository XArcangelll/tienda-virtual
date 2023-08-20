<?php

require "config/config.php";
require "config/database.php";
$db = new DataBase();

$con = $db->conectar();

$id_transsacion = isset($_GET["key"]) ? $_GET["key"] : 0;

$error = "";
if ($id_transsacion == "" || $id_transsacion == 0) {
    $error = "Error al procesar la petición";
} else {
    $sql = $con->prepare("SELECT count(id) FROM compra WHERE id_transaccion=? AND (status = ? or status = ?)");
    $sql->execute([$id_transsacion, 'COMPLETED','approved']);
    if ($sql->fetchColumn() > 0) {

        $sql = $con->prepare("SELECT id, fecha, email, total FROM compra WHERE id_transaccion=? AND (status = ? or status = ? ) LIMIT 1");
        $sql->execute([$id_transsacion, 'COMPLETED','approved']);
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $idCompra = $row["id"];
        $total = $row["total"];
        $fecha = $row["fecha"];

        $sqlDet = $con->prepare("SELECT nombre,precio,cantidad FROM detalle_compra WHERE id_compra = ?");
        $sqlDet->execute([$idCompra]);
    } else {
        $error = "Error al Comprobar la compra";
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


<?php include 'menu.php'; ?>

    <main>
        <div class="container">

            <?php if (strlen($error) > 0) { ?>
                <div class="row">
                    <div class="col">
                        <h3><?php echo $error ?></h3>
                    </div>
                </div>

            <?php } else { ?>

                <div class="row">
                    <div class="col">
                        <b>Folio de la compra: </b><?php echo $id_transsacion ?><br>
                        <b>Fecha de Compra: </b><?php echo $fecha ?><br>
                        <b>Total: </b><?php echo MONEDA . number_format($total, 2, '.', ',') ?><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cantidad </th>
                                    <th>Producto </th>
                                    <th>Importe</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) {
                                    $importe = $row_det["precio"] * $row_det["cantidad"];
                                ?>
                                    <tr>
                                        <td><?php echo $row_det["cantidad"] ?></td>
                                        <td><?php echo $row_det["nombre"] ?></td>
                                        <td><?php echo $importe ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        if (document.getElementById("CerrarSesion")) {

            document.getElementById("CerrarSesion").addEventListener("click", function() {

                let urlo = "clases/clienteAjax.php";
                let formDataa = new FormData();
                formDataa.append("action", "cerrarSesion");

                fetch(urlo, {
                    method: "POST",
                    body: formDataa
                }).then(response => response.json()).then(data => {
                    if (data.ok) {

                        window.location.href = "index.php";


                    } else {
                        console.log("No ha iniciado Sesión");
                    }
                })

            });
        }
    </script>

</body>

</html>