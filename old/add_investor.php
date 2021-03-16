<?php

$query = "INSERT INTO investor (first_name, last_name, pot_name, dob, country, currency, coins_secure_datetime, sex, net_worth, phone, email, password, investor_id)
  VALUES ('$first_name', '$last_name', '$pot_name', '$dob', '$country', '$currency', '$t_date', '$sex', 20,  '$phone', '$email', '$e_password', '$investor_id')";   

$result = $mysqli->query($query);


if ($result == "1") {

	$done = "1";
  }