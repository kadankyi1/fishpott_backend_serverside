<?php

$secret = 'g0dh6v36llth3p0w3r';


if(isset($user_id)){

  $e_user_id = md5($secret . $user_id);


} else{

	header("Location: ../index.php?error=error2");
}

if(isset($user_type)){

  $e_user_type = md5($secret . $user_type);


} 

if(isset($error)){

  $algorithm = 'rijndael-128'; // You can use any of the available
  $key = md5( "g0dh6v36llth3p0w3r", true); // bynary raw 16 byte dimension.
  $iv_length = mcrypt_get_iv_size( $algorithm, MCRYPT_MODE_CBC );
  $iv = mcrypt_create_iv( $iv_length, MCRYPT_RAND );
  $encrypted = mcrypt_encrypt( $algorithm, $key, $error, MCRYPT_MODE_CBC, $iv );
  $e_error = base64_encode( $iv . $encrypted );

}