<?php


$query = "SELECT COUNT(*) FROM kasa WHERE kaka_nake_status = 0 AND receiver_id = '$investor_id'";   
$result = $mysqli->query($query);

if (mysqli_num_rows($result) != 0) {

	$row = $result->fetch_array(MYSQLI_ASSOC);


	$unread_messages = $row["COUNT(*)"];


} else {

	$unread_messages = 0;

}