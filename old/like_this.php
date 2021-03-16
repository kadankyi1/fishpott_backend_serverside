<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

		
	require_once("config.php");

	include(ROOT_PATH . 'inc/db_connect.php');

	$likeData_news_id = trim($_POST["likeData_news_id"]);
	$likeData_investor_id = trim($_POST["likeData_investor_id"]);
	$likeData_like_type = trim($_POST["likeData_like_type"]);
	$like_data_time = date("Y-m-d H:i:s");

    $query = "SELECT like_type FROM likes WHERE liker_investor_id = '$likeData_investor_id' AND likes_news_id = '$likeData_news_id'";

    $result = $mysqli->query($query);

	if (mysqli_num_rows($result) == "0") {

		$query = "INSERT INTO likes (sku, likes_news_id, liker_investor_id, like_type, date_time)
		  VALUES ('', '$likeData_news_id', '$likeData_investor_id', '$likeData_like_type', '$like_data_time')";   


	    $result = $mysqli->query($query);

		if ($result != "0") {


			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 1";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_likes = $row["COUNT(*)"];

			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 0";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_dislikes = $row["COUNT(*)"];


			$likeReturn  = array(
				'return_status' => '1', 
				'current_likes' => $num_of_likes, 
				'current_dislikes' => $num_of_dislikes, 
				'like_type' => $likeData_like_type

				);
			echo json_encode($likeReturn,JSON_UNESCAPED_SLASHES); //exit;


			} else {

			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 1";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_likes = $row["COUNT(*)"];

			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 0";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_dislikes = $row["COUNT(*)"];


			$likeReturn  = array(
				'return_status' => '2', 
				'current_likes' => $num_of_likes, 
				'current_dislikes' => $num_of_dislikes, 
				'like_type' => $likeData_like_type

				);
			echo json_encode($likeReturn,JSON_UNESCAPED_SLASHES); //exit;
		}

	}  else {

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$db_like_type = $row["like_type"];

	if($db_like_type != $likeData_like_type) {

		$query = "UPDATE likes SET like_type = $likeData_like_type WHERE likes_news_id = '$likeData_news_id' AND liker_investor_id = '$likeData_investor_id'";
		$result = $mysqli->query($query);

		if ($result == true) {

			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 1";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_likes = $row["COUNT(*)"];

			$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 0";   


		    $result = $mysqli->query($query);
  			$row = $result->fetch_array(MYSQLI_ASSOC);
  			$num_of_dislikes = $row["COUNT(*)"];


			$likeReturn  = array(
				'return_status' => '1', 
				'current_likes' => $num_of_likes, 
				'current_dislikes' => $num_of_dislikes, 
				'like_type' => $likeData_like_type

				);
			echo json_encode($likeReturn,JSON_UNESCAPED_SLASHES); //exit;
		} else {



				$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 1";   


			    $result = $mysqli->query($query);
	  			$row = $result->fetch_array(MYSQLI_ASSOC);
	  			$num_of_likes = $row["COUNT(*)"];

				$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 0";   


			    $result = $mysqli->query($query);
	  			$row = $result->fetch_array(MYSQLI_ASSOC);
	  			$num_of_dislikes = $row["COUNT(*)"];


				$likeReturn  = array(
					'return_status' => '2', 
					'current_likes' => $num_of_likes, 
					'current_dislikes' => $num_of_dislikes, 
					'like_type' => $likeData_like_type

					);
				echo json_encode($likeReturn,JSON_UNESCAPED_SLASHES);
			}

		} else {

				$query = "DELETE FROM likes WHERE likes_news_id = '$likeData_news_id' AND liker_investor_id = '$likeData_investor_id'";
				$result = $mysqli->query($query);
				//$row = $result->fetch_array(MYSQLI_ASSOC);
				//var_dump($result);

				if ($result == true) {
  

				$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 1";   


			    $result = $mysqli->query($query);
	  			$row = $result->fetch_array(MYSQLI_ASSOC);
	  			$num_of_likes = $row["COUNT(*)"];

				$query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$likeData_news_id' AND like_type = 0";   


			    $result = $mysqli->query($query);
	  			$row = $result->fetch_array(MYSQLI_ASSOC);
	  			$num_of_dislikes = $row["COUNT(*)"];


				$likeReturn  = array(
					'return_status' => '2', 
					'current_likes' => $num_of_likes, 
					'current_dislikes' => $num_of_dislikes, 
					'like_type' => $likeData_like_type

					);
				echo json_encode($likeReturn,JSON_UNESCAPED_SLASHES);
				}		
			}

	}
}