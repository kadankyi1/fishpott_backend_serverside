<?php

$hosting = "localhost";
$user = "r3dph03n_y3nfish";
$db_connect_password = "g0tt6h6v31t";
$database = "r3dph03n_awafishpot";
		$mysqli = new mysqli($hosting, $user, $db_connect_password, $database);
		$mysqli->set_charset('utf8mb4');
		//$mysqli->set_charset('utf8mb4');
if(!isset($redirect)) {
		/* check connection */
		if ($mysqli->connect_errno) {

		$create_account = "no";
		$status = 0;
		} else {
		$create_account = "yes";
		$status = 1;

		}

} else {

		$create_account = "yes";
		$status = 1;

}

