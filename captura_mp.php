<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body>


    <div id="contenedor_carga" style="visibility:visible;opacity:1">
        <h4 class="procesando">Procesando detalle de la transacción...</h4>
        <div class="loader">
        </div>
    </div>


   

</body>


</html>




<?php
require "config/config.php";
require "config/database.php";


$db = new DataBase();

$con = $db->conectar();

$idTransaccion = isset($_GET["payment_id"]) ? $_GET["payment_id"] : '';
$status = isset($_GET["status"]) ? $_GET["status"] :  '';


if($idTransaccion != ''){

    $fecha = date("Y-m-d H:i:s");
    $monto = isset($_SESSION["carrito"]["total"]) ? $_SESSION["carrito"]["total"] : 0;
    $idCliente = $_SESSION["user_cliente"];
    $sqlEmail = $con->prepare("SELECT email FROM clientes WHERE id= ? AND status = 1");
    $sqlEmail->execute([$idCliente]);
    $row_cliente = $sqlEmail->fetch(PDO::FETCH_ASSOC);
    $email = $row_cliente["email"];

    $comando = $con->prepare("INSERT INTO compra( id_transaccion,fecha,status,medio_pago,email,id_cliente) VALUES(?,?,?,?,?,?)");
    $comando->execute([$idTransaccion,$fecha,$status,'MP',$email,$idCliente]);
    $id = $con->lastInsertId();

    if($id > 0){

        $productos = isset($_SESSION["carrito"]["productos"]) ? $_SESSION["carrito"]["productos"] : null;

        if ($productos != null) {
            foreach ($productos as $clave => $cantidad) {
        
                $sql = $con->prepare("SELECT id,nombre,precio,descuento FROM productos WHERE id= ? AND activo = 1");
                $sql->execute([$clave]);
                $row_prod = $sql->fetch(PDO::FETCH_ASSOC);

                $nombre = $row_prod["nombre"];
                $precio = $row_prod["precio"];
                $descuento = $row_prod["descuento"];
                $precio_desc = $precio - (($precio * $descuento) / 100);

                $sql_insert = $con->prepare("INSERT INTO detalle_compra (id_compra,id_producto,nombre,precio,cantidad) VALUES(?,?,?,?,?)");
                $sql_insert->execute([$id,$clave,$nombre,$precio_desc,$cantidad]);

                $monto += $cantidad * $precio_desc;
            }

            $sql = $con->prepare("UPDATE compra set total = ? where id = ? and status like 'approved'");
            $sql->execute([$monto,$id]);

            
            require 'clases/Mailer.php';

        

            $asunto = "Detalle de su compra";

            $cuerpo = '<h4>Gracias por su compra</h4>';
            $cuerpo .= '<p>El ID de su compra es <b>'. $idTransaccion .'</b><p>';
            $cuerpo .= "<br>";
            $cuerpo .= "<p>Este es el enlace de su compra</p><br>";
            $cuerpo .=  "<a href='http://localhost/tienda-virtual/completado.php?key=".$idTransaccion."' target='_blank'>Acceda aquí.</a>";

            $mailer = new Mailer();
            $mailer->enviarEmail($email,$asunto,$cuerpo);

        }

       unset($_SESSION["carrito"]);
        header("Location: " .SITE_URL."/completado.php?key=".$idTransaccion);

    }

    

}

?>
