<?php


$query = "SELECT * FROM nsesa ORDER BY sku DESC";   


$result = $mysqli->query($query);
		

if (mysqli_num_rows($result) != 0) {

$row = $result->fetch_array(MYSQLI_ASSOC);
$GHS_USD = $row["GHS_USD"];
$USD_GHS = $row["USD_GHS"];
$GHS_GBP = $row["GHS_GBP"];
$GBP_GHS = $row["GBP_GHS"];
$USD_GBP = $row["USD_GBP"];
$GBP_USD = $row["GBP_USD"];
$coins_GHS = $row["coins_GHS"];
$coins_USD = $row["coins_USD"];
$coins_GBP = $row["coins_GBP"];


} else {
	if(!isset($pot_name)) {
		include(ROOT_PATH . 'inc/auto_logout.php');
	}

}

