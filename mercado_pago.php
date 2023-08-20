<?php

require 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken('');

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001'; 
$item->title = 'Producto CDP';
$item->quantity = 1;
$item->unit_price = 150.00;
$item->currency_id = "PEN";
$preference->items = array($item);

$preference->back_urls = array(
    "success" => "http://localhost/tienda-virtual/captura.php",
    "fail" => "http://localhost/tienda-virtual/fallo.php"
);

$preference->auto_return = "approved";
$preference->binary_mode = true;

$preference->save();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>

<h3>Mercado Pago</h3>

<div id="wallet_container" ></div>

<script>
   const mp = new MercadoPago('',{
    locale: 'es-PE'
   });

   const bricksBuilder = mp.bricks();

   mp.bricks().create("wallet", "wallet_container", {
   initialization: {
       preferenceId: "<?php echo $preference->id ?>"
   }, customization: {
      texts: {
          action: 'buy',
      },
      visual: {
          buttonBackground: 'black',
          borderRadius: '16px',
      },
 },
    });

</script>
    
</body>
</html>