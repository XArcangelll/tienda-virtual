<?php

require_once '../config/config.php';
require_once '../config/database.php';
require_once 'clienteFunciones.php';

$datos = [];

if(isset($_POST["action"])){
    $action = $_POST["action"];
    $db = new DataBase();
    $con = $db->conectar();
    if($action == "existeUsuario"){
        $datos["ok"] = usuarioExiste($_POST["usuario"],$con);
    }elseif($action == "existeEmail"){
        $datos["ok"] = emailExiste($_POST["email"],$con);

    }elseif($action == "cerrarSesion"){
        $datos["ok"] = cerrarSesion();
    }

}

echo json_encode($datos);

