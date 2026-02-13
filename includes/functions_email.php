<?php
// /includes/functions_email.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключаем PHPMailer, если он установлен через Composer
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}

function sendEmail($to, $subject, $message, $from, $attachments = []) {
    global $settings;

    if ($settings['smtp_settings']['method'] === 'smtp') {
        // Используем PHPMailer для SMTP
        $mail = new PHPMailer(true);
        try {
            // Настройки сервера
            $mail->isSMTP();
            $mail->Host = $settings['smtp_settings']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $settings['smtp_settings']['username'];
            $mail->Password = $settings['smtp_settings']['password'];
            $mail->SMTPSecure = $settings['smtp_settings']['encryption'];
            $mail->Port = $settings['smtp_settings']['port'];

            // Отправитель и получатель
            $mail->setFrom($settings['smtp_settings']['from_email'], $settings['smtp_settings']['from_name']);
            $mail->addAddress($to);

            // Вложения
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment['path'], $attachment['name']);
            }

            // Контент
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->CharSet = 'UTF-8';

            return $mail->send();
        } catch (Exception $e) {
            error_log("Ошибка отправки письма через SMTP: {$mail->ErrorInfo}");
            return false;
        }
    } else {
        // Используем стандартную функцию mail()
        $boundary = md5(uniqid(time()));
        
        $headers = "From: {$settings['smtp_settings']['from_email']}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $message . "\r\n";
        
        foreach ($attachments as $attachment) {
            $file_content = file_get_contents($attachment['path']);
            $file_encoded = base64_encode($file_content);
            
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/octet-stream; name=\"{$attachment['name']}\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"\r\n\r\n";
            $body .= chunk_split($file_encoded) . "\r\n";
        }
        
        $body .= "--$boundary--";
        
        return mail($to, $subject, $body, $headers);
    }
}
?>