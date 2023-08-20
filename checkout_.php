<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?PHP CLIENT_ID ?>"></script>
</head>
<body>

   <div id="paypal-button-container" ></div>


   
<script>

  paypal.Buttons({
    style:{
      color:'blue',
      shape: 'pill',
      label: 'pay',
      size: 'responsive'
    },
    createOrder: function(data,actions){
      return actions.order.create({
        purchase_units:[{
          amount:{
            value: "300.00",
            custom: 'Maquina de guerra'
          },
        reference_id: "id_refencia_Aleatorio"
        }]
      });
    },
    onApprove: function(data,actions){
          actions.order.capture().then(function(detalles){
                    console.log(detalles);
                    console.log(data);
                    console.log(actions);
                   let paymentid = detalles.purchase_units[0].payments.captures[0].id
                   console.log(paymentid);
                window.location.href="completado.php";

            });
          
    },
    onCancel: function(data){
        alert('sigue fundionando?');
        console.log(data.orderID);
    }
  }).render('#paypal-button-container');

  </script>
    
</body>
</html>