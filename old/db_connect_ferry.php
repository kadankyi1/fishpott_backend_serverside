<?php

$hosting2 = "localhost";
$user2 = "r3dph03n_y3nfish";
$db_connect_password2 = "g0tt6h6v31t";
$database2 = "r3dph03n_ferrylisicious";
		$mysqli2 = new mysqli($hosting2, $user2, $db_connect_password2, $database2);
if(!isset($redirect)) {
		/* check connection */
		if ($mysqli->connect_errno) {

		$status2 = 0;

		} else {

		$status2 = 1;

		}

} else {

		$status2 = 1;

}

