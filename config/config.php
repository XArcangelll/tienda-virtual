<?php

date_default_timezone_set('America/Lima'); 

define('SITE_URL', 'http://localhost/tienda-virtual');


define('CLIENT_ID', 'paypal id');
define("KEY_TOKEN","AVH#SV52SA24-35SZ-2*");
define("MONEDA","S/.");
define("TOKEN_MP",'TOKEN GRANDE');
define("PUBLIC_MP","TOKEN CHICO");


//datos para el envio de correo electrónico

define("MAIL_HOST", "smtp.office365.com");
define("MAIL_USER","");
define('MAIL_PASS', '');
define('MAIL_PORT','587');



session_start();

$num_cart = 0;

if(isset($_SESSION["carrito"]["productos"])){
    $num_cart = array_sum($_SESSION["carrito"]["productos"]);
}