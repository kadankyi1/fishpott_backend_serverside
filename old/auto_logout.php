<?php

session_unset();
// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
// Finally, destroy the session.
session_destroy();

if(isset($android) && $android == 1){

		$signUpReturn  = array(
			'status' => 0, 
			'user_id' => "na", 
			'error_set' => 1, 
			'error' => "Something went Awry. We Couldn't Connect To Your Pott"

			);
		echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;

} else {

	header("Location: ../index.php?error=auto_logout");

}
