<?php
  CMSMS::sendMessage('00447911123456', 'Test message');

  class CMSMS {

    static public function buildMessageXml($recipient, $message, $sender_name, $sms_token) {
      $xml = new SimpleXMLElement('<MESSAGES/>');

      $authentication = $xml->addChild('AUTHENTICATION');
      $authentication->addChild('PRODUCTTOKEN', $sms_token);

      $msg = $xml->addChild('MSG');
      $msg->addChild('FROM', $sender_name);
      $msg->addChild('TO', $recipient);
      $msg->addChild('BODY', $message);

      return $xml->asXML();
    } // END OF buildMessageXml

    static public function sendMessage($recipient, $message, $sender_name, $sms_token) {
      $xml = self::buildMessageXml($recipient, $message, $sender_name, $sms_token);

      $ch = curl_init(); // cURL v7.18.1+ and OpenSSL 0.9.8j+ are required
      curl_setopt_array($ch, array(
          CURLOPT_URL            => 'https://sgw01.cm.nl/gateway.ashx',
          CURLOPT_HTTPHEADER     => array('Content-Type: application/xml'),
          CURLOPT_POST           => true,
          CURLOPT_POSTFIELDS     => $xml,
          CURLOPT_RETURNTRANSFER => true
        )
      );

      $response = curl_exec($ch);

      curl_close($ch);

      return $response;
    } // END  OF sendMessage

  }