<?php
use PHPMailer\PHPMailer\{PHPMailer,SMTP,Exception};


require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require  '../phpmailer/src/Exception.php';


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;     //SMTP::DEBUG_OFF                 //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'tucorreo';                     //SMTP username
    $mail->Password   = 'tucontra';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('tu correo', 'Mailer');
    $mail->addAddress('el correo del destinatario', 'Mailer');     //Add a recipient
 

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Detalle de su compra';
    $cuerpo = '<h4>Gracias por su compra</h4>';
    $cuerpo .= '<p>El ID de su compra es <b>'. $id_transaccion .'</b><p>';
    $cuerpo .= "<br>";
    $cuerpo .= "<p>Este es el enlace de su compra</p><br>";
    $cuerpo .=  "<a href='http://localhost/tienda-virtual/completado.php?key=".$id_transaccion."' target='_blank'>Acceda aquí.</a>";
    $mail->Body    = mb_convert_encoding($cuerpo, 'ISO-8859-1', 'UTF-8');
    $mail->AltBody = 'Le enviamos los detalles de su compra';

    $mail->setLanguage('es', '../phpmailer/language/phpmailer.lang-es.php');

    $mail->send();
} catch (Exception $e) {
    echo "Error al enviar el correo electrónico de la compra: {$mail->ErrorInfo}";
}