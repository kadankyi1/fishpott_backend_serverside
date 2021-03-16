<?php

$query = "SELECT * FROM $table_name WHERE $column1_name = $column1_value AND $column2_name = $column2_value";   
$result = $mysqli->query($query);

if (mysqli_num_rows($result) != 0) {

	$row = $result->fetch_array(MYSQLI_ASSOC);

	if(isset($item_1_set) && $item_1_set == 1) {

	$item_1 = $row["item_1"];

	}

	if(isset($item_2_set) && $item_2_set == 1) {

	$item_2 = $row["item_2"];

	}
	if(isset($item_3_set) && $item_3_set == 1) {

	$item_3 = $row["item_3"];

	}
	if(isset($item_4_set) && $item_4_set == 1) {

	$item_4 = $row["item_4"];

	}
	if(isset($item_5_set) && $item_5_set == 1) {

	$item_5 = $row["item_5"];

	}

	$found_item = 1;

} else {

	$found_item = 0;

}
