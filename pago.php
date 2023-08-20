<?php
require "config/config.php";
require "config/database.php";


if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

require 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken(TOKEN_MP);

$preference = new MercadoPago\Preference();

$productos_mp = array();

$db = new DataBase();

$con = $db->conectar();

$productos = isset($_SESSION["carrito"]["productos"]) ? $_SESSION["carrito"]["productos"] : null;

//print_r($_SESSION);

$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {

        $sql = $con->prepare("SELECT id,nombre,precio,descuento, $cantidad as cantidad FROM productos WHERE id= ? AND activo = 1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("location: index.php");
    exit;
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
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID ?>&currency=MXN"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>


    <div id="contenedor_carga">
        <h4 class="procesando">Procesando Compra...</h4>
        <div class="loader">
        </div>
    </div>


    <?php include 'menu.php' ?>

    <main>
        <div class="container">

            <div class="row">
                <div class="col-6">
                    <h4>Detalles de Pago</h4>
                    <div class="row">
                        <div class="col-12">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="wallet_container"></div>
                        </div>
                    </div>

                </div>

                <div class="col-6">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>SubTotal</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($lista_carrito == null) {
                                    echo '<tr><td colspan="5" class="text-center"><b>Lista Vacía</b></td></tr>';
                                } else {

                                    $total = 0;
                                    foreach ($lista_carrito as $producto) {
                                        $_id = $producto["id"];
                                        $nombre = $producto["nombre"];
                                        $precio = $producto["precio"];
                                        $cantidad = $producto["cantidad"];
                                        $descuento = $producto["descuento"];
                                        $precio_desc = $precio - (($precio * $descuento) / 100);
                                        $subtotal = $cantidad * $precio_desc;
                                        $total += $subtotal;

                                        $item = new MercadoPago\Item();
                                        $item->id = $_id;
                                        $item->title = $nombre;
                                        $item->quantity = $cantidad;
                                        $item->unit_price = $precio_desc;
                                        $item->currency_id = "PEN";

                                        array_push($productos_mp, $item);
                                        unset($item);


                                ?>
                                        <tr>
                                            <td><?php echo $nombre ?></td>
                                            <td>
                                                <div id="subtotal_<?php echo $_id ?>" name="subtotal[]">
                                                    <?php echo MONEDA . number_format($subtotal, 2, ".", ",") ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <td colspan="2">
                                            <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total, 2, ".", ",") ?></p>
                                        </td>
                                    </tr>


                            </tbody>
                        <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php


    $preference->items = $productos_mp;

    $preference->back_urls = array(
        "success" => "http://localhost/tienda-virtual/captura_mp.php",
        "fail" => "http://localhost/tienda-virtual/fallo.php"
    );

    $preference->auto_return = "approved";
    $preference->binary_mode = true;

    $preference->save();

    ?>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        paypal.Buttons({
            style: {
                color: 'blue',
                shape: 'pill',
                label: 'pay',
                size: 'responsive'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: <?php echo $total ?>
                        },
                        reference_id: "id_refencia_Aleatorio"
                    }]
                });
            },
            onApprove: function(data, actions) {
                const contenedor = document.getElementById("contenedor_carga");
                contenedor.style.visibility = "visible";
                contenedor.style.opacity = "1";
                let url = 'clases/captura.php';
                actions.order.capture().then(function(detalles) {
                    // console.log("estoy en esta parte 2");
                    // console.log(detalles);

                    return fetch(url, {
                        method: "post",
                        headers: {
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            detalles: detalles
                        })
                    }).then(function(response) {
                        // contenedor.style.visibility = "hidden";
                        //  contenedor.style.opacity = "0";

                        //  console.log("pagado");
                        window.location.href = "completado.php?key=" + detalles["id"];
                    });
                    //.then(response => response.json()).then(data => console.log(data))

                });

            },
            onCancel: function(data) {
                alert('sigue fundionando?');
                console.log(data.orderID);
            }
        }).render('#paypal-button-container');


        const mp = new MercadoPago('<?php echo PUBLIC_MP ?>', {
            locale: 'es-PE'
        });

        const bricksBuilder = mp.bricks();

        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: "<?php echo $preference->id ?>"
            },
            customization: {
                texts: {
                    action: 'buy',
                },
                visual: {
                    buttonBackground: 'black',
                    borderRadius: '16px',
                },
            },
        });

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