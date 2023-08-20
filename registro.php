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

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $email = trim($_POST["email"]);
    $telefono = trim($_POST["telefono"]);
    $dni = trim($_POST["dni"]);
    $usuario = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);
    $repassword = trim($_POST["repassword"]);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $dni, $usuario, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (!esEmail($email)) {
        $errors[] = "La dirección de correo no es válida";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (usuarioExiste($usuario, $con)) {
        $errors[] = "El nombre de usuario $usuario ya existe";
    }

    if (emailExiste($email, $con)) {
        $errors[] = "El correo electrónico $email ya existe";
    }

    if (count($errors) == 0) {

        $id = registraCliente([$nombres, $apellidos, $email, $telefono, $dni], $con);

        if ($id > 0) {

            require 'clases/Mailer.php';
            $mailer = new Mailer();
            $token = generarToken();
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $idUsuario = registraUsuario([$usuario, $pass_hash, $token, $id], $con);
            if ($idUsuario > 0) {

                $url = SITE_URL . '/activar_cliente.php?id=' . $idUsuario . '&token=' . $token;
                $asunto = "Activar cuenta - Tienda Online";
                $cuerpo = "Estimado $nombres: <br> Para continuar con el proceso de registro es indispensable dar click en la siguiente liga <a href='$url'>Activar Cuenta</a>";

                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado al correo $email";
                    exit;
                }
            } else {
                $errors[] = "error al registrar al usuario";
            }
        } else {
            $errors[] = "error al registrar al cliente";
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
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body>
    
<?php include 'menu.php' ?>

    <main>
        <div class="container">
            <h2>Datos del cliente</h2>

            <?php mostrarMensajes($errors) ?>


            <form action="registro.php" method="post" autocomplete="off" class="row g-3">
                <div class="col-md-6">
                    <label for="nombres" class="mb-3"><span class="text-danger">*</span> Nombres</label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="apellidos" class="mb-3"><span class="text-danger">*</span> Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="mb-3"><span class="text-danger">*</span> Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <span id="validaEmail" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="telefono" class="mb-3"><span class="text-danger">*</span> Teléfono</label>
                    <input type="tel" name="telefono" id="telefono" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="dni" class="mb-3"><span class="text-danger">*</span> DNI</label>
                    <input type="text" name="dni" id="dni" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="usuario" class="mb-3"><span class="text-danger">*</span> Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                    <span id="validaUsuario" class="text-danger"></span>
                </div>

                <div class="col-md-6">
                    <label for="password" class="mb-3"><span class="text-danger">*</span> Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="repassword" class="mb-3"><span class="text-danger">*</span> Repetir Contraseña</label>
                    <input type="password" name="repassword" id="repassword" class="form-control" required>
                </div>

                <i><b>Nota:</b> Los Campos Con Asterisco Son Obligatorios</i>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>

            </form>
        </div>
    </main>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        let txtUsuario = document.getElementById("usuario");
        txtUsuario.addEventListener("blur", function() {
            existeUsuario(txtUsuario.value);
        }, false);

        let txtEmail = document.getElementById("email");
        txtEmail.addEventListener("blur", function() {
            existeEmail(txtEmail.value);
        }, false);



        function existeUsuario(usuario) {
            let url = "clases/clienteAjax.php";
            let formData = new FormData();
            formData.append("action", "existeUsuario");
            formData.append("usuario", usuario);

            fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.ok) {
                    document.getElementById("usuario").value = "";
                    document.getElementById("validaUsuario").innerHTML = "Usuario no disponible";
                } else {
                    document.getElementById("validaUsuario").innerHTML = "";
                }
            })

        }


        function existeEmail(email) {
            let url = "clases/clienteAjax.php";
            let formData = new FormData();
            formData.append("action", "existeEmail");
            formData.append("email", email);

            fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.ok) {
                    document.getElementById("email").value = "";
                    document.getElementById("validaEmail").innerHTML = "Email no disponible";
                } else {
                    document.getElementById("validaEmail").innerHTML = "";
                }
            })

        }
    </script>


</body>

</html>