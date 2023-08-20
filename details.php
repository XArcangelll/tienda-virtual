<?php
require "config/config.php";
require "config/database.php";
$db = new DataBase();

$con = $db->conectar();

$id = isset($_GET["id"]) ? $_GET["id"] : "";
$token =  isset($_GET["token"]) ? $_GET["token"] : "";

if ($id == "" || $token == "") {
    echo "Error al procesar la información";
    exit;
} else {
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if ($token == $token_tmp) {



        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo = 1");
        $sql->execute([$id]);
        if ($sql->fetchColumn() > 0) {

            $sql = $con->prepare("SELECT nombre,descripcion,precio,descuento FROM productos WHERE id=? AND activo = 1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row["nombre"];
            $descripcion = $row["descripcion"];
            $precio = $row["precio"];
            $descuento = $row["descuento"];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $dir_images = "images/productos/" . $id . '/';
            $rutaimg = $dir_images . "principal.jpg";

            if (!file_exists($rutaimg)) {
                $rutaImg = "images/no-photo.jpg";
            }

            $imagenes = array();

            if (file_exists($dir_images)) {

                $dir = dir($dir_images);

                while (($archivo = $dir->read()) != false) {
                    if ($archivo != "principal.jpg" && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))) {
                        $imagenes[] =  $dir_images . $archivo;
                    }
                }
                $dir->close();

                $sqlCaracter = $con->prepare("SELECT DISTINCT(det.id_carac) as idCat, c.caracteristica FROM det_prod_carac AS det INNER JOIN caracteristicas AS c ON det.id_carac= c.id WHERE det.id_prod = ?");
                $sqlCaracter->execute([$id]);
            }
        } else {
            echo "error al procesar la petición";
            exit;
        }
    } else {
        echo "error al procesar la petición";
        exit;
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

    <main>
        <div class="container">

            <div class="row">

                <div class="col-md-6 order-md-1">


                    <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo $rutaimg ?>" class="d-block w-100">
                            </div>


                            <?php foreach ($imagenes as $img) { ?>

                                <div class="carousel-item">
                                    <img src="<?php echo $img ?>" class="d-block w-100">
                                </div>
                            <?php } ?>


                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                </div>

                <div class="col-md-6 order-md-2">
                    <h2><?php echo $nombre ?></h2>

                    <?php if ($descuento > 0) { ?>
                        <p><del><?php echo MONEDA .  number_format($precio, 2, ".", ",") ?></del></p>
                        <h2><?php echo MONEDA .  number_format($precio_desc, 2, ".", ",") ?><small class="text-success"> <?php echo $descuento ?>% descuento</small></h2>


                    <?php } else { ?>
                        <h2><?php echo MONEDA .  number_format($precio, 2, ".", ",") ?></h2>
                    <?php } ?>

                    <p class="lead">
                        <?php echo $descripcion ?>
                    </p>

                    <div class="col-3 my-3">

                        <?php while ($row_cat = $sqlCaracter->fetch(PDO::FETCH_ASSOC)) {
                            $idCat = $row_cat["idCat"];
                            echo $row_cat["caracteristica"];
                        ?>
                            <select class="form-select my-2" id="<?php echo "cat_" . $idCat ?>">
                                <?php

                                $sqlDet = $con->prepare("SELECT id,valor,stock FROM det_prod_carac where id_prod=? and id_carac = ?");
                                $sqlDet->execute([$id, $idCat]);

                                while ($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <option id="<?php echo $row_det["id"] ?>"><?php echo $row_det["valor"] ?></option>
                                <?php

                                }
                                ?>

                            </select>
                        <?php


                        } ?>

                    </div>

                    <div class="col-3 my-3">
                        Cantidad: <input value="1" min="1" type="number" max="10" name="cantidad" id="cantidad" class="form-control" type="text">
                    </div>

                    <div class="d-grid gap-3 col-10 mx-auto">
                        <button class="btn btn-primary" type="button">Comprar Ahora</button>
                        <button class="btn btn-outline-primary" type="button" onclick="addProducto(<?php echo $id; ?>,cantidad.value, '<?php echo $token_tmp; ?>')">Agregar al Carrito</button>
                    </div>


                </div>

            </div>



        </div>
    </main>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        function addProducto(id, cantidad, token) {

            const url = 'clases/carrito.php'
            const formData = new FormData()
            formData.append('id', id);
            formData.append('cantidad', cantidad);
            formData.append('token', token);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        let elemento = document.getElementById("num_cart");
                        elemento.innerHTML = data.numero;

                        document.getElementById("cantidad").value = 1;
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