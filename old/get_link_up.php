<?php
//session_start();
if(isset($_GET['linkUpAjaxCheck'])) {
session_start();	
require_once("config.php");


	include(ROOT_PATH . 'inc/get_fold.php');

	include(ROOT_PATH . 'inc/set_check_login_type.php');

	include(ROOT_PATH . 'inc/id_unfold.php');
	include(ROOT_PATH . 'inc/db_connect.php');
}

$status = 1;
$counter = 0;
for ($counter; $counter <= 20; $counter++) {

  $query = "SELECT * FROM investor ORDER BY RAND() LIMIT 1";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $link_investor_id = $row["investor_id"];
      $link_profile_picture = $row["profile_picture"];
	  $link_first_name = $row["first_name"];
	  $link_last_name = $row["last_name"];
	  $link_full_name = $link_first_name . " " . $link_last_name;

        	$query = "SELECT * FROM linkups WHERE (sender_id = '$link_investor_id' AND receiver_id = '$investor_id') OR (sender_id = '$investor_id' AND receiver_id = '$link_investor_id')";

		  	//$numrows = mysql_num_rows($query);
		 	 $result = $mysqli->query($query);

		  	if (mysqli_num_rows($result) == 1) {

		      $row = $result->fetch_array(MYSQLI_ASSOC);
				//if ($link_investor_id != $investor_id)	{
				  	  if($counter == 20) {

				  	  	$status = 0;
						$show_link_up = 0; 
						if(isset($_GET['linkUpAjaxCheck'])) {
							$linkUpInfo  = array(
								'set' => 0, 
								'link_id' => $link_investor_id, 
								'link_pic' =>  $link_profile_picture, 
								'link_name' => $link_full_name
								);
							echo json_encode($linkUpInfo,JSON_UNESCAPED_SLASHES); exit;
						}
						
  	  }
		  	} else {

		  		if ($link_investor_id != $investor_id)	{
		      			$status = 0;
		  				$show_link_up = 1;

		  				if(isset($_GET['linkUpAjaxCheck'])) {
		  					//echo $link_profile_picture; exit;
		  					$linkUpInfo  = array(
		  						'set' => 1, 
		  						'link_id' => $link_investor_id, 
		  						'link_pic' =>  $link_profile_picture, 
		  						'link_name' => $link_full_name
		  						);
		  					echo json_encode($linkUpInfo,JSON_UNESCAPED_SLASHES); exit;
		  				}

		  		}
		  	}

	  } else {

		  					$linkUpInfo  = array(
		  						'set' => 0, 
		  						'link_id' => $link_investor_id, 
		  						'link_pic' =>  $link_profile_picture, 
		  						'link_name' => $link_full_name
		  						);
		  					echo json_encode($linkUpInfo,JSON_UNESCAPED_SLASHES); exit;
	  }
}