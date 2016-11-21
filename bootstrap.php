<?php
error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/vendor/autoload.php';

use Guzzle\Service\Client;

$text = $_POST['text'];
$token = $_POST['token'];

$secret = 'enter_your_token';

if ($token !== $secret) {
   $msg = "The token for the slash command doesn't match. Check your script.";
   header('HTTP/1.0 401 Unauthorized');
   die($msg);
}

echo (new \ChatBot\PhpManualChatBot(new Client()))
    ->lookupFunction($text)
    ->getMethodDescription();