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

          $mail->Body  = '
          <!DOCTYPE html>
          <html lang="en">
          <head>
            <meta charset="UTF-8">
            <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
            <title>Document</title>
          </head>
          <body>
          <div class="container">
            <div class="page-header">
                <h1>'.$body['msgtitle'].'</h1>
            </div>
            <div class="panel panel-primary">
                <div class="panel-body">
                    '.$body['msgbody'].'
                </div>
            </div>
            <hr>
            <small>Sistem Pengelolaan Keuangan Sekolah</small>
          </div>
          </body>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
          </html>

          ';

          $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          if(!$mail->send()) {
              $output = 'Mailer Error: ' . $mail->ErrorInfo;
          } else {
              $output = 'Message has been sent';
          }

          return $output;
    }


}
