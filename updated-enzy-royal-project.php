<?php

$to = "enzyroyal@gmail.com";

$name = "Daniella";
$email = "sommaxruby@gmail.com";
$message = "Updated Enzy Royal project";

$subject = "Portfolio Contact Form";

$body = "Name: Daniela";
$body .= "Email: sommaxruby@gmail.com";
$body .= "Message: php mailer";

$headers = "From: sommaxruby@gmail.com";

to(mail($to, $subject, $body, $headers))
 {"Message sent successfully!";}
} {
 "Failed to send message.";
}

?>