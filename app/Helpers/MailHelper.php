<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    public static function send($to, $subject, $content)
    {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình Server SMTP
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['MAIL_PORT'] ?? 465;
            $mail->CharSet    = 'UTF-8';

            // Người gửi và người nhận
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@travelvn.com', $_ENV['MAIL_FROM_NAME'] ?? 'TravelVN');
            $mail->addAddress($to);

            // Nội dung Email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $content;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Lỗi gửi Email: {$mail->ErrorInfo}");
            return false;
        }
    }
}