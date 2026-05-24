<?php
namespace App\Services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

final class EmailService {
    public static function sendMail(array $arrayTeacher, string $affair, string $body) {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 465);

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME'] ?? 'Gestión de Sustituciones');
            $mail->addAddress($arrayTeacher['email'], $arrayTeacher['name']);

            $logoPath = __DIR__ . '/../../public/assets/img/isotype.png';
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_centro');
            }

            $mail->isHTML(true);
            $mail->Subject = $affair;
            $mail->Body    = $body;

            return $mail->send();

        } catch (Exception $e) {
            throw new Exception("Error al enviar correo: {$mail->ErrorInfo}");
        }
    }
}
