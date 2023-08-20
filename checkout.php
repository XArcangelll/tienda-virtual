<?php
require "config/config.php";
require "config/database.php";
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

    <main>
        <div class="container">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
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

                        ?>
                                <tr>
                                    <td><?php echo $nombre ?></td>
                                    <td><?php echo MONEDA . number_format($precio_desc, 2, ".", ",") ?></td>
                                    <td>
                                        <input type="number" min="1" max="10" step="1" value="<?php echo $cantidad ?>" size="5" id="cantidad_<?php echo $_id ?>" onchange="actualizaCantidad(this.value, <?php echo $_id ?>)">
                                    </td>
                                    <td>
                                        <div id="subtotal_<?php echo $_id ?>" name="subtotal[]">
                                            <?php echo MONEDA . number_format($subtotal, 2, ".", ",") ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal">Eliminar</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td colspan="3"></td>
                                <td colspan="2">
                                    <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, ".", ",") ?></p>
                                </td>
                            </tr>


                    </tbody>
                <?php } ?>
                </table>
            </div>

            <?php
            if ($lista_carrito != null) {
            ?>

                <div class="row">
                    <div class="col-md-5 offset-md-7 d-grid gap-2">
                        <?php if(isset($_SESSION["user_cliente"])) { ?>
                        <a href="pago.php" class="btn btn-primary btn-lg">Realizar Pago</a href="pago.php">
                        <?php }else{?>
                            <a href="login.php?pago" class="btn btn-primary btn-lg">Realizar pago</a href="pago.php">
                            <?php }?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>


    <div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Desea Eliminar el Producto de la lista?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn-elimina" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        let eliminaModal = document.getElementById("eliminaModal");


        eliminaModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget;
            let id = button.getAttribute('data-bs-id');
            let buttonElimina = this.querySelector('.modal-footer #btn-elimina');
            buttonElimina.value = id;
        });

        function actualizaCantidad(cantidad, id) {

            const url = 'clases/actualizar_carrito.php'
            const formData = new FormData()
            formData.append("action", "agregar")
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {

                        let divsubtotal = document.getElementById('subtotal_' + id)
                        divsubtotal.innerHTML = data.sub;

                        let total = 0.00;

                        let list = document.getElementsByName("subtotal[]");

                        for (let i = 0; i < list.length; i++) {
                            total += parseFloat(list[i].innerText.slice(3).replace(/[,]/g, ''));
                        }

                        total = new Intl.NumberFormat('en-US', {
                            minimumFractionDigits: 2
                        }).format(total);

                        document.getElementById("total").innerHTML = '<?php echo MONEDA ?>' + total;

                    }
                })

        }


        function eliminar() {

            let botonElimina = document.getElementById("btn-elimina");
            let id = botonElimina.value;

            const url = 'clases/actualizar_carrito.php'
            const formData = new FormData()
            formData.append("action", "eliminar")
            formData.append('id', id);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        location.reload();
                    }
                })

        }

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