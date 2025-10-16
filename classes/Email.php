<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;
    public function __construct($email, $nombre, $token){
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
       //Crear el objeto de email
       $mail = new PHPMailer();

       // Looking to send emails in production? Check out our Email API/SMTP product!
       $mail->isSMTP(TRUE);
       $mail->Host = 'sandbox.smtp.mailtrap.io';
       $mail->SMTPAuth = true;
       $mail->Port = 2525;
       $mail->Username = 'db96df2801b678';
       $mail->Password = '8c5f30011ba5f4';

       $mail->setFrom('cuentas@appsalon.com');

       $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
       $mail->Subject ='Confirma tu Cuenta';

       //Set HTML
       $mail->isHTML(TRUE);
       $mail->CharSet = 'UTF-8';

       $contenido = "<html>";
       $contenido .= "<p><strong>Hola ".$this->nombre . "</strong> Te has convertido en un Cavernicola. Para confirmarlo presiona en el siguiente enlace</p>";
       $contenido .="<p>Presiona aquí: <a href='http://localhost:8000/confirmar-cuenta?token=".$this->token ."'>Confirmar Cuenta</a></p>";
       $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
       $contenido .= "</html>";

       $mail->Body = $contenido;

       //Enviar el mail
       $mail->send();
    }

    public function enviarInstrucciones(){
       //Crear el objeto de email
       $mail = new PHPMailer();

       $mail->isSMTP(TRUE);
       $mail->Host = 'sandbox.smtp.mailtrap.io';
       $mail->SMTPAuth = true;
       $mail->Port = 2525;
       $mail->Username = 'db96df2801b678';
       $mail->Password = '8c5f30011ba5f4';

       $mail->setFrom('cuentas@appsalon.com');

       $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
       $mail->Subject ='Restablecer Contraseña';

       //Set HTML
       $mail->isHTML(TRUE);
       $mail->CharSet = 'UTF-8';

       $contenido = "<html>";
       $contenido .= "<p><strong>Hola ".$this->nombre . "</strong> Has solicitado restablecer tu contraseña, sigue el siguiente enlace para hacerlo</p>";
       $contenido .="<p>Presiona aquí: <a href='http://localhost:3000/recuperar?token=".$this->token ."'>Restablecer Contraseña</a></p>";
       $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
       $contenido .= "</html>";

       $mail->Body = $contenido;

       //Enviar el mail
       $mail->send();
    }
}
