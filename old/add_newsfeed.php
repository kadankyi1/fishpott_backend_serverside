<?php
session_start();
require_once("config.php");


include(ROOT_PATH . 'inc/get_fold.php');

include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');


$news_id = uniqid($investor_id, TRUE);

$newsType = $_POST['news_type'];
$newsType = trim($newsType);


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
		if(isset($_FILES["upload_news_pic"]["name"])) {

				$target_dir = "../user/news_files/pics/";
				$target_pic = $target_dir . $ext . basename($_FILES["upload_news_pic"]["name"]);
				$pic_db_name = "news_files/pics/" . $ext . basename($_FILES["upload_news_pic"]["name"]);
				$imgAdd = 1;
				$imageFileType = pathinfo($target_pic,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {
				    $check = getimagesize($_FILES["upload_news_pic"]["tmp_name"]);
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
				if ($_FILES["upload_news_pic"]["size"] > 5 * MB) {
				        $imgAdd = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
				        $imgAdd = 0;
				}
			}

		// CHECKS FOR VIDEO
		if(isset($_FILES["upload_news_video"]["name"])) {

				$video_target_dir = "../user/news_files/videos/";
				$target_video = $video_target_dir . $ext . basename($_FILES["upload_news_video"]["name"]);
				$video_db_name = "news_files/videos/" . $ext . basename($_FILES["upload_news_video"]["name"]);
				$videoAdd = 1;
				$videoFileType = pathinfo($target_video,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				// Check if file already exists
				if (file_exists($target_video)) {
				        $videoAdd = 0;
				}
				// Check file size
				if ($_FILES["upload_news_video"]["size"] > 20 * MB) {
				        $videoAdd = 0;
				}
				// Allow certain file formats
				if($videoFileType != "mp4" && $videoFileType != "mkv" && $videoFileType != "webm" && $videoFileType != "ogg" ) {
				        $videoAdd = 0;
				}

			}

			if(!isset($imgAdd)) { $imgAdd = 0;}
			if(!isset($videoAdd)) { $videoAdd = 0;}
		// Check if $uploadOk is set to 0 by an error
		if(isset($_POST['addNewsText']) && $_POST['addNewsText'] != "") {
				$addNewsText = $_POST['addNewsText'];
				//$addNewsText = mysqli_real_escape_string($mysqli, $addNewsText);
				$textAdd = 1;
			} else {

				$textAdd = 0;
			}
		include(ROOT_PATH . 'inc/db_connect.php');

		 if ($status == 1) {
				if ($imgAdd == 0 && $videoAdd == 0 && $textAdd == 0) {
					$error = "No News";
				} elseif ($videoAdd == 1 && $textAdd == 1) {
		    		if (move_uploaded_file($_FILES["upload_news_video"]["tmp_name"], $target_video)) {

		    			$pic_db_name = "";
						$table_name = "newsfeed";

						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = $addNewsText;
						$column7_value = $pic_db_name;
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

						$opt = $_SESSION["newsfeed"];

						if($pic_db_name == "" || $video_db_name=""){
				        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
				        }
		    		} else {

			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
		    		}
				} elseif ($videoAdd == 1 && $textAdd == 0) {
		    		if (move_uploaded_file($_FILES["upload_news_video"]["tmp_name"], $target_video)) {

		    			$pic_db_name = "";
						$table_name = "newsfeed";

						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = "";
						$column7_value = $pic_db_name;
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

						$opt = $_SESSION["newsfeed"];

						if($pic_db_name == "" || $video_db_name=""){
				        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
				        }
		    		} else {

			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
		    		}
				} elseif ($imgAdd == 1 && $textAdd == 1) {
		    		if (move_uploaded_file($_FILES["upload_news_pic"]["tmp_name"], $target_pic)) {

		    			$video_db_name = "";
						$table_name = "newsfeed";

						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = $addNewsText;
						$column7_value = $pic_db_name;
						$column8_value = "";

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";
						$pam8 = "s";
						include(ROOT_PATH . 'inc/insert8_prepared_statement.php');

						$opt = $_SESSION["newsfeed"];

						if($pic_db_name == "" || $video_db_name=""){
				        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
				        }
		    		} else {

			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
		    		}

				}	elseif ($imgAdd == 0 && $videoAdd == 0 && $textAdd == 1) {


		    			$video_db_name = "";
		    			$pic_db_name = "";
						$table_name = "newsfeed";

						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = $addNewsText;
						$column7_value = $pic_db_name;
						$column8_value = "";

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";
						$pam8 = "s";
						include(ROOT_PATH . 'inc/insert8_prepared_statement.php');

						$opt = $_SESSION["newsfeed"];

						if($pic_db_name == "" || $video_db_name=""){
				        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
				        }

				} elseif ($imgAdd == 1 && $videoAdd == 0 && $textAdd == 0) {

					//echo $_FILES["upload_news_pic"]["tmp_name"] . "<br>"; exit;
		    		if (move_uploaded_file($_FILES["upload_news_pic"]["tmp_name"], $target_pic)) {

		    			$addNewsText = "";
		    			$video_db_name = "";
						$table_name = "newsfeed";

						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = $addNewsText;
						$column7_value = $pic_db_name;
						$column8_value = "";

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";
						$pam8 = "s";
						include(ROOT_PATH . 'inc/insert8_prepared_statement.php');

						$opt = $_SESSION["newsfeed"];

						if($pic_db_name == "" || $video_db_name=""){
				        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
				        }
		    		} else {

			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
		    		}

				}

				if($newsType == "up4sale") {


					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "up4sale";
					$item_name = $_POST['item_name'];
					$set_price = $_POST['set_price'];
					$item_current_location = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];
					$delivery_style = $_POST['delivery_style'];

					//echo $set_price; exit;

					$column1_name = "up4sale_news_id";
					$column2_name = "item_name";
					$column3_name = "item_location";
					$column4_name = "item_payment_way";
					$column5_name = "item_delivery";
					$column6_name = "seller_id";
					$column7_name = "item_description";
					$column8_name = "item_price";

					$column1_value = $news_id;
					$column2_value = $item_name;
					$column3_value = $item_current_location;
					$column4_value = $payment_way;
					$column5_value = $delivery_style;
					$column6_value = $investor_id;
					$column7_value = $addNewsText;
					$column8_value = $set_price;

					$pam1 = "s";
					$pam2 = "s";
					$pam3 = "s";
					$pam4 = "s";
					$pam5 = "s";
					$pam6 = "s";
					$pam7 = "s";
					$pam8 = "s";

					include(ROOT_PATH . 'inc/insert8_prepared_statement.php');

					if($pic_db_name == "" || $video_db_name = ""){
			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }

				} elseif($newsType == "shares4sale") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "shares4sale";
					$chosen_shares = $_POST['myshares'];
					$set_price = $_POST['set_price'];
					$num_on_sale = $_POST['target'];
					$payment_way = $_POST['payment_way'];

					//echo $set_price; exit;

					$column1_name = "shares4sale_owner_id";
					$column2_name = "sharesOnSale_id";
					$column3_name = "selling_price";
					$column4_name = "num_on_sale";
					$column5_name = "shares_news_id";

					$column1_value = $investor_id;
					$column2_value = $chosen_shares;
					$column3_value = $set_price;
					$column4_value = $num_on_sale;
					$column5_value = $news_id;

					$pam1 = "s";
					$pam2 = "s";
					$pam3 = "s";
					$pam4 = "s";
					$pam5 = "s";
					include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
					//echo $done; exit;

					if($pic_db_name == "" || $video_db_name=""){
			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }

				} elseif($newsType == "fundraiser") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "fundraiser";
					$fundraiser_name = $_POST['item_name'];
					$set_price = $_POST['set_price'];
					$target_amount = $_POST['target'];
					$closing_date = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];

					//echo $set_price; exit;


						$column1_name = "f_starter_id";
						$column2_name = "fundraiser_id";
						$column3_name = "start_date";
						$column4_name = "end_date";
						$column5_name = "f_payment_way";
						$column6_name = "target_amount";
						$column7_name = "f_news_id";
						$column8_name = "fundraiser_name";

						$column1_value = $investor_id;
						$column2_value = $investor_id . uniqid($ext, TRUE);
						$column3_value = date("Y-m-d");
						$column4_value = $closing_date;
						$column5_value = $payment_way;
						$column6_value = $target_amount;
						$column7_value = $news_id;
						$column8_value = $fundraiser_name;

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "d";
						$pam7 = "s";
						$pam8 = "s";
						//echo "here"; exit;
					include(ROOT_PATH . 'inc/insert8_prepared_statement.php');
					if($pic_db_name == "" || $video_db_name=""){
			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }
				} elseif($newsType == "event") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "event";
					$event_name = $_POST['item_name'];
					$ticket_price = $_POST['set_price'];
					$num_of_avai_tickets = $_POST['target'];
					$event_venue = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];

					$event_datetime = $_POST['event_time'];
					$event_datetime = new DateTime($event_datetime); 


					$event_date = $event_datetime->format('l jS F Y');
					$event_time = $event_datetime->format('g:ia');

					//echo $set_price; exit;


						$column1_name = "creater_id";
						$column2_name = "event_id";
						$column3_name = "event_name";
						$column4_name = "image";
						$column5_name = "venue";
						$column6_name = "event_date";
						$column7_name = "event_time";
						$column8_name = "ticket_cost";
						$column9_name = "event_news_id";
						$column10_name = "num_of_goers";

						$column1_value = $investor_id;
						$column2_value = uniqid($ext, TRUE);
						$column3_value = $event_name;
						$column4_value = $pic_db_name;
						$column5_value = $event_venue;
						$column6_value = $event_date;
						$column7_value = $event_time;
						$column8_value = $ticket_price;
						$column9_value = $news_id;
						$column10_value = "";

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";
						$pam8 = "s";
						$pam9 = "s";
						$pam10 = "s";
					include(ROOT_PATH . 'inc/insert10_prepared_statement.php');

					if($pic_db_name == "" || $video_db_name=""){
			        	header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }

				} 
		}

		include(ROOT_PATH . 'inc/db_connect.php');

		 if ($status == 1 && $pic_db_name != "") {

					$table_name = "photos";

						$column1_name = "sku";
						$column2_name = "p_owner_id";
						$column3_name = "p_pic_path";

						$column1_value = "";
						$column2_value = $investor_id;
						$column3_value = $pic_db_name;

						$pam1 = "i";
						$pam2 = "s";
						$pam3 = "s";

					include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			        header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");

		 }

		include(ROOT_PATH . 'inc/db_connect.php');

		 if ($status == 1 && $video_db_name != "") {

					$table_name = "videos";

						$column1_name = "sku";
						$column2_name = "v_owner_id";
						$column3_name = "v_video_path";

						$column1_value = "";
						$column2_value = $investor_id;
						$column3_value = $video_db_name;

						$pam1 = "i";
						$pam2 = "s";
						$pam3 = "s";

					include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			        header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");

		 }
