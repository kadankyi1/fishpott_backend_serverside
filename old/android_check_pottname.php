<?php

if(
    isset($_POST['checkpottnamecode']) && 
    trim($_POST['checkpottnamecode']) == "thejoyofchildrenlaughingaroundyou" && 
    isset($_POST['this_pott_name']) &&  
    trim($_POST['this_pott_name']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $this_pott_name = mysqli_real_escape_string($mysqli, $_POST['this_pott_name']);

    $this_pott_name = trim($this_pott_name);
    $this_pott_name = strtolower($this_pott_name);

    if(strlen($this_pott_name) < 5){
        echo "3"; exit;
    }



    if(strlen($this_pott_name) > 20){
        echo "4"; exit;
    }

	if (strpos($this_pott_name, '_') !== false) {

	    $testPottname =  str_replace("_","",$this_pott_name);
	    if (ctype_alpha($testPottname)) {
	        //echo "The string $testcase consists of all letters.\n";
	    } else {
	          echo "2"; exit;
	    }

	}  else {

	    if (ctype_alpha($this_pott_name)) {
	        //echo "The string $testcase consists of all letters.\n";
	    } else {
	          echo "2"; exit;
	    }

	}

if(trim($this_pott_name) == "mylinkups" || trim($this_pott_name) == "@mylinkups"){

          echo "0"; exit;
          
}



    $query = "SELECT fcm_token FROM investor WHERE pot_name = '$this_pott_name'";   
    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          echo "0"; exit;
      } else {
        echo "1"; exit;
      }


}
?>