<?php
session_start();
/*

i   corresponding variable has type integer
d   corresponding variable has type double
b   corresponding variable is a blob and will be sent in packets

*/

/* create a prepared statement */

if(isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
	require_once("config.php");
	$s_index = $_POST["s_index"];
	$_SESSION[$s_index][7] = $_POST["item_quant"];
	$_SESSION[$s_index][8] = "Pay On Delivery";
	$_SESSION[$s_index][4] = $_POST["add"] . ",  " . $_POST["reg"] . ", " . $_POST["cntry"];
	$_SESSION[$s_index][5] = $_POST["this_lat"];
	$_SESSION[$s_index][6] = $_POST["this_long"];
	echo 1;

}


?>