<?php
session_start();
require_once("config.php");

include(ROOT_PATH . 'inc/id_unfold.php');

$old_e_login = $_SESSION["login_type"];
$old_e_u_type = $_SESSION["user_type"];

include(ROOT_PATH . 'inc/set_check_login_type.php');

		define('KB', 1024);
		define('MB', 1048576);
		define('GB', 1073741824);
		define('TB', 1099511627776);


		$t = time();
		$r_t = date("Y-m-d",$t);
		$ext = $r_t . $t;

		$date_time = date("Y-m-d H:i:s");
		$date_time = trim($date_time);

		// CHECKS FOR IMAGE
		if(isset($_FILES["msg_pic"]["name"]) && $_FILES["msg_pic"]["name"] != "") {

				$target_dir = "../user/msg_files/pics/";
				$target_pic = $target_dir . $ext . basename($_FILES["msg_pic"]["name"]);
				$pic_db_name = "msg_files/pics/" . $ext . basename($_FILES["msg_pic"]["name"]);
				$imgAdd = 1;
				$imageFileType = pathinfo($target_pic,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {
				    $check = getimagesize($_FILES["msg_pic"]["tmp_name"]);
				    if($check !== false) {
				        $imgAdd = 1;
				    } else {
				        $imgAdd = 0;		    
				    }
				}
				// Check if file already exists
				if (file_exists($target_pic)) {
				        $imgAdd = 0;
				}
				// Check file size
				if ($_FILES["msg_pic"]["size"] > 5 * MB) {
				        $imgAdd = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
				        $imgAdd = 0;
				}
			} else {

				$pic_db_name = "";
			}


		// CHECKS FOR VIDEO
		if(isset($_FILES["msg_vid"]["name"])) {

				$video_target_dir = "../user/msg_files/videos/";
				$target_video = $video_target_dir . $ext . basename($_FILES["msg_vid"]["name"]);
				$video_db_name = "msg_files/videos/" . $ext . basename($_FILES["msg_vid"]["name"]);
				$videoAdd = 1;
				$videoFileType = pathinfo($target_video,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				// Check if file already exists
				if (file_exists($target_video)) {
				        $videoAdd = 0;
				}
				// Check file size
				if ($_FILES["msg_vid"]["size"] > 20 * MB) {
				        $videoAdd = 0;
				}
				// Allow certain file formats
				if($videoFileType != "mp4" && $videoFileType != "mkv" && $videoFileType != "webm" && $videoFileType != "ogg" ) {
				        $videoAdd = 0;
				}

			} else {

				$video_db_name = "";

			}

		if(isset($_FILES["msg_aud"]["name"])) {

				$aud_target_dir = "../user/msg_files/aud/";
				$target_aud = $aud_target_dir . $ext . basename($_FILES["msg_aud"]["name"]);
				$aud_db_name = "msg_files/aud/" . $ext . basename($_FILES["msg_aud"]["name"]);
				$audAdd = 1;
				$audFileType = pathinfo($target_aud,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				// Check if file already exists
				if (file_exists($target_aud)) {
				        $audAdd = 0;
				}
				// Check file size
				if ($_FILES["msg_aud"]["size"] > 20 * MB) {
				        $audAdd = 0;
				}
				// Allow certain file formats
				if($audFileType != "mp3" && $audFileType != "ogg" ) {
				        $audAdd = 0;
				}

			} else {

				$aud_db_name = "";

			}


			if(!isset($imgAdd)) { $imgAdd = 0;}
			if(!isset($videoAdd)) { $videoAdd = 0;}
			if(!isset($audAdd)) { $audAdd = 0;}
		// Check if $uploadOk is set to 0 by an error
		if(isset($_POST['kasa']) && $_POST['kasa'] != "") {
				$kasa = $_POST['kasa'];
				//$kasa = mysqli_real_escape_string($mysqli, $kasa);
				$textAdd = 1;
			} else {

				$textAdd = 0;
			}

		include(ROOT_PATH . 'inc/db_connect.php');

		 if ($status == 1) {
				if ($imgAdd == 0 && $videoAdd == 0 && $audAdd == 0 && $textAdd == 0) {
					exit;
				} 

				if ($videoAdd == 1) {
		    		if (move_uploaded_file($_FILES["msg_vid"]["tmp_name"], $target_video)) {

		    			$video_added = 1;
		    			$dontsendmsg = 0;
		    		} else {

		    			$video_added = 0;
		    			$dontsendmsg = 1;
		    		}
				} else {
		    		
					$video_db_name = "";
				}

				if ($audAdd == 1) {
		    		if (move_uploaded_file($_FILES["msg_aud"]["tmp_name"], $target_aud)) {

		    			$aud_added = 1;
		    			$dontsendmsg = 0;
		    		} else {

		    			$aud_added = 0;
		    			$dontsendmsg = 1;
		    		}
				}  else {

					$aud_db_name = "";
				}

				if ($imgAdd == 1) {

		    		if (move_uploaded_file($_FILES["msg_pic"]["tmp_name"], $target_pic)) {

		    			$img_added = 1;
		    			$dontsendmsg = 0;
		    		} else {

		    			$img_added = 0;
		    			$dontsendmsg = 1;
		    		}

				}  else {

					$pic_db_name = "";
				}

				if(!isset($_FILES["msg_pic"]["name"]) || trim($_FILES["msg_pic"]["name"]) == "") {
					
					$dontsendmsg = 1;

				} else {

					$dontsendmsg = 0;
				}

				if(!isset($_FILES["msg_aud"]["name"]) || trim($_FILES["msg_aud"]["name"]) == "") {
					
					$dontsendmsg = 1;

				} else {

					$dontsendmsg = 0;
				}

				if(!isset($_FILES["msg_vid"]["name"]) || trim($_FILES["msg_vid"]["name"]) == "") {
					
					$dontsendmsg = 1;

				} else {

					$dontsendmsg = 0;
				}

				if(trim($_POST['kasa']) == "" || trim($_POST['kasa']) == "Type message") {
					
					$dontsendmsg = 1;

				} else {

					$dontsendmsg = 0;
				}


				if(!isset($dontsendmsg)) { $dontsendmsg = 1;}

/*
			echo "pic_db_name : " . $pic_db_name . "\n";
			echo "aud_db_name : " . $aud_db_name . "\n";
			echo "video_db_name : " . $video_db_name . "\n"; 
			echo "kasa : " . $kasa . "\n"; 
			echo "imgAdd : " . $imgAdd . "\n"; 
			echo "audAdd : " . $audAdd . "\n"; 
			echo "videoAdd : " . $videoAdd . "\n"; 
			echo "kasa : " . $kasa . "\n"; 
			echo "dontsendmsg : " . $dontsendmsg . "\n"; 
			var_dump($_POST);
			exit;
*/

				$done = 0;
				if($dontsendmsg == 0) {
						$table_name = "kasa";

						$column1_name = "akasakasa_id";
						$column2_name = "sender_id";
						$column3_name = "receiver_id";
						$column4_name = "kasa";
						$column5_name = "kasa_date_time";
						$column6_name = "kasa_pic";
						$column7_name = "kasa_aud";
						$column8_name = "kasa_vid";

						$column1_value = $_POST['akasakasa_id'];
						$column2_value = $investor_id;
						$column3_value = $_POST['receiver_id'];
						$column4_value = $kasa;
						$column5_value = $date_time;
						$column6_value = $pic_db_name;
						$column7_value = $aud_db_name;
						$column8_value = $video_db_name;

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";
						$pam8 = "s";
						include(ROOT_PATH . 'inc/insert8_prepared_statement.php');
						$cur_akasa_id = trim($_POST['akasakasa_id']);
						$opt = $_SESSION["newsfeed"];

						if($done == 1) {
							$msg_added = 1;

							include(ROOT_PATH . 'inc/db_connect.php');

					          $skip = 1;
							$table_name = "akasakasa";
							$item_1 = "latest_kasa_sku";
							$item_2 = "latest_date_time";
							$column1_name = "akasakasa_id";
							$column1_value = "$cur_akasa_id";
							$pam1 = "s";

							include(ROOT_PATH . 'inc/select2_where1_prepared_statement.php');
							include(ROOT_PATH . 'inc/db_connect.php');
							
							if($skip == 0 && trim($item_1) != "" && $item_1 != "latest_kasa_sku" && $done == 1) {

					        $query = "SELECT sku, kasa_date_time FROM kasa WHERE akasakasa_id = '$cur_akasa_id' ORDER BY sku DESC";


					          //$numrows = mysql_num_rows($query);
					          $result = $mysqli->query($query);

					          if (mysqli_num_rows($result) != "0") {

					              $row = $result->fetch_array(MYSQLI_ASSOC);
					              $kasa_date_time = $row["kasa_date_time"];
					              $sku = $row["sku"];
					              $sku = intval($sku);


									$query = "UPDATE  akasakasa SET  latest_kasa_sku =  $sku, latest_date_time =  '$kasa_date_time', akasakasa_type = ''  WHERE akasakasa_id =  '$cur_akasa_id'";
												
									$result = $mysqli->query($query);


									if ($result == "1") {

										$done = 1;
										$akasakasa_update = 1;
									  } else {

										$akasakasa_update = 0;
									  }

					            } else {

										$akasakasa_update = 0;

					            }
							} else {


									$table_name = "akasakasa";

									$column1_name = "akasakasa_id";
									$column2_name = "sender_id";
									$column3_name = "receiver_id";
									$column4_name = "latest_kasa_sku";
									$column5_name = "latest_date_time";

									$column1_value = $_POST['akasakasa_id'];
									$column2_value = $investor_id;
									$column3_value = $_POST['receiver_id'];
									$column4_value = 1;
									$column5_value = $date_time;

									$pam1 = "s";
									$pam2 = "s";
									$pam3 = "s";
									$pam4 = "i";
									$pam5 = "s";

									include(ROOT_PATH . 'inc/insert5_prepared_statement.php');

									if($done == 1) {

										$akasakasa_update = 1;

									} else {

										$akasakasa_update = 0;

									}
						}
						
						} else {

							$msg_added = 0;
						}

						
					}
				}

				if(!isset($akasakasa_update)) { $akasakasa_update = 0;}
				if(!isset($msg_added)) { $msg_added = 0;}
				if(!isset($sku)) { $sku = "old_sku";}

$msg_inserted_response  = array(
	'akasakasa_update' => $akasakasa_update, 
	'msg_added' => $msg_added, 
	'latest_kasa_sku' => $sku
	);
echo json_encode($msg_inserted_response,JSON_UNESCAPED_SLASHES);
//echo $pic_db_name;
