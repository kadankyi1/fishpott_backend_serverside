<?php

$query = "UPDATE  $table_name SET  $column1_name =  '$column1_value', $column2_name =  '$column2_value' 
			WHERE $row_check =  '$row_check_value'";
			
$result = $mysqli->query($query);


if ($result == "1") {

	$done = 1;
  }