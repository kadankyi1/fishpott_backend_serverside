<?php

class preparedStatement {

	private $stmt = null;
	private $fetch_result = array();

	// PREPARING THE STATEMENT
	function prepareAndExecuteStatement($mysqli_db_connection, $sql_statement, $bind_number, $param_string, $column_values_array){
		if($mysqli_db_connection !== false){
			$stmt = $mysqli_db_connection->prepare($sql_statement);
			if($bind_number == 1){
				$stmt->bind_param($param_string, $column_values_array[0]);
			} else if($bind_number == 2){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1]);
			} else if($bind_number == 3){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2]);
			} else if($bind_number == 4){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3]);
			} else if($bind_number == 5){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4]);
			} else if($bind_number == 6){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5]);
			} else if($bind_number == 7){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6]);
			} else if($bind_number == 8){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7]);
			} else if($bind_number == 9){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8]);
			} else if($bind_number == 10){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9]);
			} else if($bind_number == 11){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10]);
			} else if($bind_number == 12){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11]);
			} else if($bind_number == 13){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12]);
			} else if($bind_number == 14){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13]);
			} else if($bind_number == 15){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14]);
			} else if($bind_number == 16){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14], $column_values_array[15]);
			} else if($bind_number == 17){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14], $column_values_array[15], $column_values_array[16]);
			} else if($bind_number == 18){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14], $column_values_array[15], $column_values_array[16], $column_values_array[17]);
			} else if($bind_number == 19){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14], $column_values_array[15], $column_values_array[16], $column_values_array[17], $column_values_array[18]);
			} else if($bind_number == 20){
				$stmt->bind_param($param_string, $column_values_array[0], $column_values_array[1], $column_values_array[2], $column_values_array[3], $column_values_array[4], $column_values_array[5], $column_values_array[6], $column_values_array[7], $column_values_array[8], $column_values_array[9], $column_values_array[10], $column_values_array[11], $column_values_array[12], $column_values_array[13], $column_values_array[14], $column_values_array[15], $column_values_array[16], $column_values_array[17], $column_values_array[18], $column_values_array[19]);
			}

			$status = $stmt->execute();
			if($status === false){
				return false;
			} else {
				return $stmt;
			}
		} else {
			return false;
		}
	}

	function getPreparedStatementQueryResults($stmt, $fetch_columns_array, $return_values_bind_number, $return_array){
		if($return_array == 1){
			if($return_values_bind_number == 1){
				$stmt->bind_result($fetch_columns_array[0]);
			} else if($return_values_bind_number == 2){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1]);
			} else if($return_values_bind_number == 3){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2]);
			} else if($return_values_bind_number == 4){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3]);
			} else if($return_values_bind_number == 5){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4]);
			} else if($return_values_bind_number == 6){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5]);
			} else if($return_values_bind_number == 7){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6]);
			} else if($return_values_bind_number == 8){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7]);
			} else if($return_values_bind_number == 9){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8]);
			} else if($return_values_bind_number == 10){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9]);
			} else if($return_values_bind_number == 11){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10]);
			} else if($return_values_bind_number == 12){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11]);
			} else if($return_values_bind_number == 13){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12]);
			} else if($return_values_bind_number == 14){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13]);
			} else if($return_values_bind_number == 15){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14]);
			} else if($return_values_bind_number == 16){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14], $fetch_columns_array[15]);
			} else if($return_values_bind_number == 17){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14], $fetch_columns_array[15], $fetch_columns_array[16]);
			} else if($return_values_bind_number == 18){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14], $fetch_columns_array[15], $fetch_columns_array[16], $fetch_columns_array[17]);
			} else if($return_values_bind_number == 19){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14], $fetch_columns_array[15], $fetch_columns_array[16], $fetch_columns_array[17], $fetch_columns_array[18]);
			} else if($return_values_bind_number == 20){
				$stmt->bind_result($fetch_columns_array[0], $fetch_columns_array[1], $fetch_columns_array[2], $fetch_columns_array[3], $fetch_columns_array[4], $fetch_columns_array[5], $fetch_columns_array[6], $fetch_columns_array[7], $fetch_columns_array[8], $fetch_columns_array[9], $fetch_columns_array[10], $fetch_columns_array[11], $fetch_columns_array[12], $fetch_columns_array[13], $fetch_columns_array[14], $fetch_columns_array[15], $fetch_columns_array[16], $fetch_columns_array[17], $fetch_columns_array[18], $fetch_columns_array[19]);
			} else {
				return false;
			}
			$stmt->fetch();
			return $fetch_columns_array;
		} else {
			return $stmt;
		}
	}

	function closePreparedStatementAndDatabaseConnection($stmt){
		$stmt->close();
	}

}	