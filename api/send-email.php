<?php
/**
 * Email Sending Endpoint using PHPMailer
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [
    'success' => false,
    'message' => 'An error occurred'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $name = isset($input['name']) ? trim($input['name']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $message = isset($input['message']) ? trim($input['message']) : '';

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters';
    }

    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SENDER_EMAIL, SENDER_NAME);
        $mail->addAddress(ADMIN_EMAIL, 'Daniella Portfolio');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission from {$name}";

        $htmlBody = "
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px; }
                    .content { background: #f7f7f7; padding: 20px; margin-top: 20px; border-radius: 5px; }
                    .field { margin: 15px 0; }
                    .label { font-weight: bold; color: #667eea; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Contact Form Submission</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <span class='label'>Name:</span><br>
                            " . htmlspecialchars($name) . "
                        </div>
                        <div class='field'>
                            <span class='label'>Email:</span><br>
                            <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
                        </div>
                        <div class='field'>
                            <span class='label'>Message:</span><br>
                            " . nl2br(htmlspecialchars($message)) . "
                        </div>
                    </div>
                </div>
            </body>
        </html>
        ";

        $mail->Body = $htmlBody;
        $mail->AltBody = "Name: {$name}\nEmail: {$email}\nMessage: {$message}";

        if ($mail->send()) {
            $response['success'] = true;
            $response['message'] = 'Email sent successfully!';
            http_response_code(200);
        } else {
            throw new Exception('Email sending failed');
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        error_log('Email Error: ' . $e->getMessage());
        http_response_code(500);
    }
} else {
    $response['message'] = 'Invalid request method';
    http_response_code(405);
}

echo json_encode($response);
?>
