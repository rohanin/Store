<?php

$host = "localhost";
$dbname = "shop_db";
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;

if ($mysqli->affected_rows){

    require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="http://localhost/project/Store/send-password-reset.php/token=$token">here</a>
    to reset your password.

    END;

    try {

        $mail->send();

    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mal->ErrorInfo}";

    }

}

echo "Message sent, please check your inbox.";