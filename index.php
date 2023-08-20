<?php
require "config/config.php";
require "config/database.php";
$db = new DataBase();

$con = $db->conectar();

$sql = $con->prepare("SELECT id,nombre,precio FROM productos WHERE activo = 1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

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
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

                <?php foreach ($resultado as $row) {

                    $id = $row["id"];
                    $imagen = "images/productos/" . $id . "/principal.jpg";

                    if (!file_exists($imagen)) {
                        $imagen = "images/no-photo.jpg";
                    }


                ?>

                    <div class="col">

                        <div class="card shadow-sm h-100">


                            <img src="<?php echo $imagen ?>" class="card-img-top">


                            <div class="card-body">
                                <p class="card-title"><?php echo $row["nombre"] ?></p>
                                <p class="card-text"><strong>S/. <?php echo number_format($row["precio"], 2, '.', ',') ?></strong></p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <a href="details.php?id=<?php echo $row["id"]; ?>&token=<?php echo hash_hmac('sha1', $row["id"], KEY_TOKEN) ?>" class="btn btn-primary">Detalles</a>
                                    </div>
                                    <button class="btn btn-success" type="button" onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN) ?>')">Agregar al Carrito</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
    </main>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



    <script>
        function addProducto(id, token) {

            const url = 'clases/carrito.php'
            const formData = new FormData()
            formData.append('id', id);
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
                    }
                })

        }


        /*   const a = [
               {id:0, name: "Miguel"},
               {id:1, name: "Justin"}
           ]

           const b = [
               {id:0, email: "miguel@aea.com"},
               {id:2, email: "pachamanca@aea.com"}
           ]

           const expectedOutput = [
               {id:0,name:"Miguel",email:"miguel@aea.com"}
           ]

           function innerJoin({leftArray,rightArray,key}){
               const map = new Map();
               leftArray.forEach(item=> map.set(item[key],item))

               //console.log(map);
               
               let join = [];


               rightArray.forEach(rightItem => {
                   const leftItem = map.get(rightItem[key]);
                   if(leftItem == undefined) return

                   join.push({...leftItem, ...rightItem});

               })

              return join

           }

          /*console.log(
           innerJoin({ leftArray: a ,rightArray: b, key:'id'})
           );*/

        let frases = "Los Gatos Domésticos son geniales";

        //console.log(frases.split(" ").slice(0,3).join(" "));

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