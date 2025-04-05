<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $phonenumber = htmlspecialchars($_POST["Message"]);
    $idcard = htmlspecialchars($_POST["Phone"]);

    $mail = new PHPMailer(true);

    try {
        // Konfigurimi për SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'milot.verbani233@gmail.com'; // Vendos emailin tënd të Gmail
        $mail->Password = 'vink aiqv zvdr llmq'; // Vendos fjalëkalimin e emailit
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Dërguesi dhe marrësi
        $mail->setFrom($email, $name);
        $mail->addAddress('milot.verbani233@gmail.com'); // Emaili ku do të dërgohet mesazhi

        // Përmbajtja e emailit
        $mail->isHTML(true);
        $mail->Subject = "$name  shkruan nga support website";
        $mail->Body = "<p><strong>Emri:</strong> $name</p><p><strong>Email:</strong> $email</p><p><strong>phonenumber:</strong><br>$idcard</p> <p><strong>Message:</strong><br>$phonenumber</p>";

        $mail->send();
        echo "<script>
        alert('Mesazhi u dergua me sukses!');
        window.location.href = 'index.php';
      </script>";
    } catch (Exception $e) {
        $error_message = "Mesazhi nuk u dërgua. Error: {$mail->ErrorInfo}";
    }
}
?>

