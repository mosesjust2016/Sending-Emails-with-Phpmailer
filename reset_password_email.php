<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Dotenv\Dotenv;
  
require 'vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$send_to_email = "mjjustme26@gmail.com";
$email_password = $_ENV["EMAIL_PASS"] ?? '';
$email_user = $_ENV["EMAIL_USER"] ?? '';
$email_host = $_ENV["MAIL_HOST"] ?? '';
$email_port = $_ENV["MAIL_PORT"] ?? '';
$otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

$response = array();

$json = send_email_message($send_to_email, $otp, $email_host, $email_user, $email_password, $email_port);

$result = json_decode($json);

if($result->code == 200){   
    $response['success'] = "1";
    $response['message'] = "success";

    echo json_encode($response);
}else{

    $response['success'] = "0";
    $response['message'] = "failed";

    echo json_encode($response);

}




function send_email_message($send_to, $send_otp, $host, $username, $password, $port){

        $body = file_get_contents('reset-password.html');
    
        $mail = new PHPMailer(true);
        
        try {

            $mail->SMTPDebug = 0;                                       
            $mail->isSMTP();                                            
            $mail->Host       = $host;                    
            $mail->SMTPAuth   = true;                             
            $mail->Username   = $username;                 
            $mail->Password   = $password;                        
            $mail->SMTPSecure = 'ssl';                              
            $mail->Port       = $port;  
        
            $mail->setFrom($username, 'Name of Sender');           
            $mail->addAddress($send_to);
            
            $mail->isHTML(true);                                  
            $mail->Subject = 'Subject';
            $mail->Body = str_replace('{{otp}}', $send_otp, $body);
            $mail->send();


            // set response code
            http_response_code(200);
            // display message: Message sent
            return json_encode(array('code' => http_response_code(), "message"=>"Mail has been sent successfully!."));
        } catch (Exception $e) {

             // set response code
             http_response_code(400);
             // display message: Message Failed 
             return json_encode(array('code' => http_response_code(), "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"));
         
        }

}


  
?>


