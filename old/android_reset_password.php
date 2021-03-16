<?php
if(isset($_POST['info']) && trim($_POST['info'])) {

		$phone = $_POST['info'];

		$subject = "Password Reset";
		$message = "Password Reset  for : " . $phone;

			  $headers = "From: <info@fishpott.com>FISHPOTT App";
			  mail("info@fishpott.com",$subject,$message,  $headers);
			  echo "sent";
}
