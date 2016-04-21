<?php
namespace App\Helper;

class Mailer{


function __construct(){


}

  function send($subject,$body,$recipient){
      $mail = new \PHPMailer();

          //$mail->SMTPDebug = 3;                               // Enable verbose debug output

          $mail->isSMTP();                                      // Set mailer to use SMTP
          $mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                               // Enable SMTP authentication
          $mail->Username = 'hprasetyou@gmail.com';                 // SMTP username
          $mail->Password = 'anakpintar';                           // SMTP password
          $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 465;                                    // TCP port to connect to

          $mail->setFrom('hprasetyou@gmail.com', 'NO REPLY');
          $mail->addAddress($recipient);     // Add a recipient

          $mail->Subject = $subject;

          $mail->Body    = $body;

          $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          if(!$mail->send()) {
              $output = 'Mailer Error: ' . $mail->ErrorInfo;
          } else {
              $output = 'Message has been sent';
          }

          return $output;
    }

}
