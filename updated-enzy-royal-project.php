<?php

$to = "enzyroyal@gmail.com";

$name = "Daniella";
$email = "sommaxruby@gmail.com";
$message = "Updated Enzy Royal project";

$subject = "Portfolio Contact Form";

$body = "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n\n";
$body .= "Message: " . $message;

$headers = "From: " . $email;

if(mail($to, $subject, $body, $headers)) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}

?>
