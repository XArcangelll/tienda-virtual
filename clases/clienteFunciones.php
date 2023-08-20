<?php

function esNulo(array $parametros){

foreach($parametros as $parametro){
    if(strlen(trim($parametro)) < 1){
        return true;
    }

    return false;
}
}


function esEmail($email){
    if(filter_var($email,FILTER_VALIDATE_EMAIL)){
        return true;
    }

    return false;
}

function validaPassword($password,$repassword){

    if(strcmp($password,$repassword) === 0){
            return true;
    }

    return false;

}


function generarToken(){

    return md5(uniqid(mt_rand(),false));

}

function registraCliente( array $datos, $con){

    $sql = $con->prepare("INSERT INTO clientes(nombres, apellidos, email, telefono, dni, status, fecha_alta) VALUES(?,?,?,?,?,1,now())");

    if($sql->execute($datos)){
        return $con->lastInsertId();
    }

    return 0;

}

function registraUsuario( array $datos, $con){

    $sql = $con->prepare("INSERT INTO usuarios(usuario,password,token,id_cliente) VALUES(?,?,?,?)");

    if($sql->execute($datos)){
        return $con->lastInsertId();
    }

    return 0;

}


function usuarioExiste($usuario,$con){

    $sql = $con->prepare("SELECT id FROM usuarios where usuario like ? LIMIT 1");
    $sql->execute([$usuario]);
    if($sql->fetchColumn() > 0){
        return true;
    }

    return false;

}


function emailExiste($email,$con){

    $sql = $con->prepare("SELECT id FROM clientes where email like ? LIMIT 1");
    $sql->execute([$email]);
    if($sql->fetchColumn() > 0){
        return true;
    }

    return false;

}

function validaToken($id,$token,$con){

    $msg = [];
    $sql = $con->prepare("SELECT id FROM usuarios where id = ? AND token like ? LIMIT 1");
    $sql->execute([$id,$token]);
    if($sql->fetchColumn() > 0){
            if(activarUsuario($id,$con)){
                $msg["mensaje"] = "Cuenta Activada";
                $msg["ok"] = "success";
            }else{
                $msg["mensaje"] = "Error al activar cuenta";
                $msg["ok"] = "danger";
            }
    }else{
        $msg["mensaje"] = "No existe el registro del cliente";
        $msg["ok"] = "danger";
    }

    return $msg;

}

function activarUsuario($id,$con){
    $sql = $con->prepare("UPDATE usuarios SET activacion = 1, token = '' WHERE id = ?");
    return $sql->execute([$id]);
}



function mostrarMensajes(array $errors){
    if(count($errors) > 0){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><ul>';
        foreach($errors as $error){
            echo '<li>' .$error .'</li>';
        }
        echo '<ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

function login($usuario,$password,$con,$proceso){
    $sql = $con->prepare("SELECT id,usuario, password,id_cliente FROM usuarios where usuario like ? LIMIT 1");
    $sql->execute([$usuario]);
    if($row = $sql->fetch(PDO::FETCH_ASSOC)){
       if(esActivo($usuario,$con)){
              if(password_verify($password,$row["password"])){
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_name"] = $row["usuario"];
                $_SESSION["user_cliente"] = $row["id_cliente"];
                if($proceso == "pago"){
                    header("Location: checkout.php");
                }else{
                    header("Location: index.php");
                }
             
                exit;
              }
       }else{
        return "El usuario no ha sido activado";
       }
    }

    return "El usuario y/o contraseña son incorrectos";
}

function esActivo($usuario,$con){
    $sql = $con->prepare("SELECT activacion FROM usuarios where usuario like ? LIMIT 1");
    $sql->execute([$usuario]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if($row["activacion"] == 1){
        return true;
    }

    return false;
}

function cerrarSesion(){
        if(isset($_SESSION["user_id"]) && isset($_SESSION["user_name"]) && isset( $_SESSION["user_cliente"])){
            
        unset($_SESSION["user_id"]);
        unset($_SESSION["user_name"]);
        unset($_SESSION["user_cliente"]);
            return true;
        }
        return false;;
}


function solicitaPassword($user_id,$con){
    $token = generarToken();
    $sql = $con->prepare("UPDATE usuarios set token_password = ?, password_request = 1 WHERE id = ?");
    if($sql->execute([$token,$user_id])){
        return $token;
    }
    return null;
}

function verificaTokenRequest($user_id,$token,$con){
    $sql = $con->prepare("SELECT id FROM usuarios WHERE id = ? AND token_password like ? AND password_request = 1 LIMIT 1");
    $sql->execute([$user_id,$token]);
    if($sql->fetchColumn() > 0){
        return true;
    }
    return false;

}

function actualizaPassword($user_id,$password,$con){

    $sql = $con->prepare("UPDATE usuarios SET password = ?, token_password = '', password_request = 0 WHERE id = ?");
    if($sql->execute([$password,$user_id])){
        return true;
    }

    return false;

}
