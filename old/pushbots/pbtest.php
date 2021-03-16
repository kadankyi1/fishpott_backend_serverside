<?php
require_once('PushBots.class.php');
$pb = new PushBots();
// Application ID
$appID = '59a3c99a4a9efa218d8b4567';
// Application Secret
$appSecret = 'dd555bdbe3fc0f161306a86f761fce34';
$sound = 'notification.wav';
$pb->App($appID, $appSecret);
$pb->Alias("john");

// Notification Settings
$pb->Alert("Test for John");
$pb->SoundOne($sound);
//$pb->Badge($badge);
$pb->Platform(array("0", "1"));
$pb->Push();


// SENDER ID : 596200744668
// SERVER KEY : AIzaSyBcfS8JMWEGo0IdaiqXCYoFgg222qjurpQ