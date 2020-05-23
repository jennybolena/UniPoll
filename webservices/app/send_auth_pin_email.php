<?php
header("Content-type:application/json");
    // Get student email
    $email = isset($_POST['email']) ? $_POST['email'] : die("email is missing");

    // Include third party library classes
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../util/phpmailer/Exception.php';
    require '../util/phpmailer/PHPMailer.php';
    require '../util/phpmailer/SMTP.php';
    include_once '../util/objects/config/Database.php';

    // Generate PIN
    $pin = generatePIN(4);

    // Send email
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; // TLS only
    $mail->SMTPSecure = 'tls'; // ssl is depracated
    $mail->SMTPAuth = true;
    $mail->Username = "unipoll.info@gmail.com";
    $mail->Password = "22920276324story";
    $mail->setFrom("unipoll.info@gmail.com", "Unipoll");
    $mail->addAddress($email);
    $mail->Subject = 'UniPoll authentication PIN'; 
    $mail->msgHTML("Authentication PIN: ". $pin);
    $mail->AltBody = "Authentication PIN: ". $pin;
    if(!$mail->send()){
        echo json_encode(["status" => 0, "error_msg" => "mail was not sent"]);
    }else{
        echo json_encode(["status" => 1, "pin" => $pin]);
    }

    // Generates a PIN
    function generatePIN($digits = 4){
        $i = 0;
        $pin = "";
        while($i < $digits){
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }