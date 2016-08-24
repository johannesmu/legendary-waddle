<?php
include("../includes/session.php");
include("../config/configuration.php");
// If you are using Composer
//require 'vendor/autoload.php';

// If you are not using Composer (recommended)
require("sendgrid.php");

$from = new SendGrid\Email(null, "test@example.com");
$subject = "Your Password Reset Email";
$to = new SendGrid\Email(null, "test@example.com");
$content = new SendGrid\Content("text/plain", "Hello, Email!");
$mail = new SendGrid\Mail($from, $subject, $to, $content);
//get your own api key
$apiKey = getenv($sendgridapikey);
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
echo $response->headers();
echo $response->body();
?>