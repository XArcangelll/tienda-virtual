<?php

require 'config/config.php';

$payment = $_GET["payment_id"];
$status = $_GET["status"];
$payment_type = $_GET["payment_type"];
$order_id = $_GET["merchant_order_id"];

echo "<h3>Pago exitoso</h3>";

echo $payment . "<br>" . $status . "<br>" . $payment_type . "<br>" . $order_id;

unset($_SESSION["carrito"]);


