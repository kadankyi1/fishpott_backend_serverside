<?php

	$query = "SELECT $column_name FROM	$user_table WHERE $column_name = '$check' OR $column2_name = '$check2' ";

	//$numrows = mysql_num_rows($query);
	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {

		$create_account = "no";

		}