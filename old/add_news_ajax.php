<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/id_unfold.php');
$old_fold = $_SESSION["e_user"];

$old_e_login = $_SESSION["login_type"];
$old_e_u_type = $_SESSION["user_type"];

include(ROOT_PATH . 'inc/set_check_login_type.php');

if(isset($_POST["ajax"]) && $_POST["ajax"] == 1 && isset($_POST["third_eye"]) && $_POST["third_eye"] == 1){

	$investor_id = "t3e";
	$_POST["ajax"] = 0;
}

$news_id = uniqid($investor_id, TRUE);

$newsType = $_POST['news_type'];
//$addNewsText = $_POST['addNewsText'];
if(isset($_POST['share_news_id'])){

$share_news_id = $_POST['share_news_id'];

} else {

$share_news_id = "";

}



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
			} else {

				$pic_db_name = "";
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

			} else {

				$video_db_name = "";

			}

		// CHECKS FOR VIDEO
		if(isset($_FILES["upload_news_aud"]["name"])) {

				$aud_target_dir = "../user/news_files/auds/";
				$target_aud = $aud_target_dir . $ext . basename($_FILES["upload_news_aud"]["name"]);
				$aud_db_name = "news_files/auds/" . $ext . basename($_FILES["upload_news_aud"]["name"]);
				$audAdd = 1;
				$audFileType = pathinfo($target_aud,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				// Check if file already exists
				if (file_exists($target_aud)) {
				        $audAdd = 0;
				}
				// Check file size
				if ($_FILES["upload_news_aud"]["size"] > 20 * MB) {
				        $audAdd = 0;
				}
				// Allow certain file formats
				if($audFileType != "mp3" && $audFileType != "ogg" ) {

				        $audAdd = 0;
				}

			} else {

				$aud_db_name = "";

			}

			if(!isset($audAdd)) { $audAdd = 0;}
			if(!isset($imgAdd)) { $imgAdd = 0;}
			if(!isset($videoAdd)) { $videoAdd = 0;}
		// Check if $uploadOk is set to 0 by an error
		if(isset($_POST['addNewsText']) && $_POST['addNewsText'] != "") {
				$addNewsText = $_POST['addNewsText'];
				//$addNewsText = mysqli_real_escape_string($mysqli, $addNewsText);
				$textAdd = 1;
				preg_match_all("/\B@[a-zA-Z0-9]+/i", $addNewsText, $mentions);
				$mentions = array_map(function($str){ return substr($str, 1); }, $mentions[0]);
				foreach($mentions as $mentionedUser){

					$table_name = "investor";
					$item_1 = "investor_id";
					$column1_name = "pot_name";
					$column1_value = $mentionedUser;
					$pam1 = "s";
					include(ROOT_PATH . 'inc/db_connect.php');
					include(ROOT_PATH . 'inc/select1_where1_prepared_statement.php');
					include(ROOT_PATH . 'inc/db_connect.php');
					if($item_1 != "" && $item_1 != "investor_id") {
							$tagged_i = $item_1;
							$table_name = "mafrewo";

							$column1_name = "news_id";
							$column1_value = $news_id;

							$column2_name = "i_tagged_id";							
							$column2_value = $tagged_i;

							$column3_name = "tagger_id";							
							$column3_value = $investor_id;

							$pam1 = "s";
							$pam2 = "s";
							$pam3 = "s";

							include(ROOT_PATH . 'inc/insert3_prepared_statement.php');
							include(ROOT_PATH . 'inc/db_connect.php');
    $new_str = '<a href="../' . $mentionedUser . '" style=" text-decoration: none; font-weight : bold; font-style: italic;  font-color: blue;">' .$mentionedUser . '</a>';
    $addNewsText = str_replace($mentionedUser, $new_str , $addNewsText);

					  }

					}



            preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $addNewsText, $match);
                $match = $match[0];
                foreach($match as $url){
    $new_str = '<a href="' . $url . '" style=" text-decoration: none; font-weight : bold; font-style: italic;  font-color: blue;" target="_blank">' .$url . '</a>';
    $addNewsText = str_replace($url, $new_str , $addNewsText);
                      }

function check_https($url){
$ch = curl_init ('https://'.$url);

curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); //its a  HEAD
curl_setopt ($ch, CURLOPT_NOBODY, true);          // no body

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);  // in case of redirects
curl_setopt ($ch, CURLOPT_VERBOSE,        0); //turn on if debugging
curl_setopt ($ch, CURLOPT_HEADER,         1);     //head only wanted

curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);    // we dont want to wait forever

curl_exec ( $ch ) ;

$header = curl_getinfo($ch,CURLINFO_HTTP_CODE);
//var_dump ($header);

if($header===0){//no ssl
        return false;
}else{//maybe you want to check for 200
        return true;
}

}
            preg_match_all('#\bwww[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $addNewsText, $match);
                $match = $match[0];
                foreach($match as $url){


$urlIshttps =  check_https($url);

if($urlIshttps == 1){
    $new_str = '<a href="https://' . $url . '" style=" text-decoration: none; font-weight : bold; font-style: italic;  font-color: blue;" target="_blank">' .$url . '</a>';

} else {

    $new_str = '<a href="http://' . $url . '" style=" text-decoration: none; font-weight : bold; font-style: italic;  font-color: blue;" target="_blank">' .$url . '</a>';
}
unset($urlIshttps);
    $addNewsText = str_replace($url, $new_str , $addNewsText);
                      }


			} else {

				$textAdd = 0;
			}

		include(ROOT_PATH . 'inc/db_connect.php');

		 if ($status == 1) {

				if ($videoAdd == 1) {

		    		if (move_uploaded_file($_FILES["upload_news_video"]["tmp_name"], $target_video)) {

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
		    		if (move_uploaded_file($_FILES["upload_news_aud"]["tmp_name"], $target_aud)) {

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

		    		if (move_uploaded_file($_FILES["upload_news_pic"]["tmp_name"], $target_pic)) {

		    			$img_added = 1;
		    			$dontsendmsg = 0;
		    		} else {

		    			$img_added = 0;
		    			$dontsendmsg = 1;
		    		}

				}  else {

					$pic_db_name = "";
				}
						$table_name = "newsfeed";
						$column1_name = "type";
						$column2_name = "inputtor_type";
						$column3_name = "inputtor_id";
						$column4_name = "news_id";
						$column5_name = "date_time";
						$column6_name = "news";
						$column7_name = "news_image";
						$column8_name = "news_video";
						$column9_name = "news_aud";
						$column10_name = "news_id_ref";

						$column1_value = $newsType;
						$column2_value = $inputtor_type;
						$column3_value = $investor_id;
						$column4_value = $news_id;
						$column5_value = $date_time;
						$column6_value = $addNewsText;
						$column7_value = $pic_db_name;
						$column8_value = $video_db_name;
						$column9_value = $aud_db_name;
						$column10_value = $share_news_id;

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

						$opt = $_SESSION["newsfeed"];



				if($newsType == "up4sale") {


					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "up4sale";
					$item_name = $_POST['item_name'];
					$set_price = $_POST['set_price'];
					$item_current_location = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];
					$delivery_style = $_POST['delivery_style'];
					$this_currency = $_POST['this_currency'];
					$item_quantity = $_POST['item_quantity'];

					//echo $set_price; exit;

					$column1_name = "up4sale_news_id";
					$column2_name = "item_name";
					$column3_name = "item_location";
					$column4_name = "item_payment_way";
					$column5_name = "item_delivery";
					$column6_name = "seller_id";
					$column7_name = "item_description";
					$column8_name = "item_price";
					$column9_name = "currency";
					$column10_name = "item_quantity";


					$column1_value = $news_id;
					$column2_value = $item_name;
					$column3_value = $item_current_location;
					$column4_value = $payment_way;
					$column5_value = $delivery_style;
					$column6_value = $investor_id;
					$column7_value = $addNewsText;
					$column8_value = $set_price;
					$column9_value = $this_currency;
					$column10_value = $item_quantity;

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

				} elseif($newsType == "shares4sale") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "shares4sale";
					$chosen_shares = $_POST['myshares'];
					$set_price = $_POST['set_price'];
					$num_on_sale = $_POST['target'];
					$payment_way = $_POST['payment_way'];
					$this_currency = $_POST['this_currency'];
					$parent_shares_id = $_POST['item_current_location'];
/*
				echo "newsType : " . $newsType . "<br>";
				echo "chosen_shares : " . $chosen_shares . "<br>";
				echo "set_price : " . $set_price . "<br>";
				echo "num_on_sale : " . $num_on_sale . "<br>";
				echo "payment_way : " . $payment_way . "<br>";
				echo "this_currency : " . $this_currency . "<br>";
				echo "parent_shares_id : " . $parent_shares_id . "<br>"; exit;
*/
					$column1_name = "shares4sale_owner_id";
					$column2_name = "sharesOnSale_id";
					$column3_name = "selling_price";
					$column4_name = "num_on_sale";
					$column5_name = "shares_news_id";
					$column6_name = "currency";
					$column7_name = "parent_shares_id";

					$column1_value = $investor_id;
					$column2_value = $chosen_shares;
					$column3_value = $set_price;
					$column4_value = $num_on_sale;
					$column5_value = $news_id;
					$column6_value = $this_currency;
					$column7_value = $parent_shares_id;

					$pam1 = "s";
					$pam2 = "s";
					$pam3 = "s";
					$pam4 = "i";
					$pam5 = "s";
					$pam6 = "s";
					$pam7 = "s";
					include(ROOT_PATH . 'inc/insert7_prepared_statement.php');
					//echo $done; exit;

					if($pic_db_name == "" || $video_db_name=""){
			        	//header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }

				} elseif($newsType == "fundraiser") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "fundraiser";
					$fundraiser_name = $_POST['item_name'];
					$set_price = $_POST['set_price'];
					$target_amount = $_POST['target'];
					$closing_date = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];
					$this_currency = $_POST['this_currency'];

					//echo $set_price; exit;


						$column1_name = "f_starter_id";
						$column2_name = "fundraiser_id";
						$column3_name = "start_date";
						$column4_name = "end_date";
						$column5_name = "f_payment_way";
						$column6_name = "target_amount";
						$column7_name = "f_news_id";
						$column8_name = "fundraiser_name";
						$column9_name = "currency";

						$column1_value = $investor_id;
						$column2_value = $investor_id . uniqid($ext, TRUE);
						$column3_value = date("Y-m-d");
						$column4_value = $closing_date;
						$column5_value = $payment_way;
						$column6_value = $target_amount;
						$column7_value = $news_id;
						$column8_value = $fundraiser_name;
						$column9_value = $this_currency;

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "d";
						$pam7 = "s";
						$pam8 = "s";
						$pam9 = "s";
						//echo "here"; exit;
					include(ROOT_PATH . 'inc/insert9_prepared_statement.php');
					if($pic_db_name == "" || $video_db_name=""){
			        	//header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }
				} elseif($newsType == "event") {

					include(ROOT_PATH . 'inc/db_connect.php');
					$table_name = "event";
					$event_name = $_POST['item_name'];
					$ticket_price = $_POST['set_price'];
					$num_of_avai_tickets = $_POST['target'];
					$event_venue = $_POST['item_current_location'];
					$payment_way = $_POST['payment_way'];
					$this_currency = $_POST['this_currency'];

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
						$column10_name = "currency";

						$column1_value = $investor_id;
						$column2_value = uniqid($ext, TRUE);
						$column3_value = $event_name;
						$column4_value = $pic_db_name;
						$column5_value = $event_venue;
						$column6_value = $event_date;
						$column7_value = $event_time;
						$column8_value = $ticket_price;
						$column9_value = $news_id;
						$column10_value = $this_currency ;

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
			        	//header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");
			        }

				} 
		}


		 if ($status == 1 && $pic_db_name != "") {
		
		include(ROOT_PATH . 'inc/db_connect.php');

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

			        //header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");

		 }


		 if ($status == 1 && $video_db_name != "") {

		include(ROOT_PATH . 'inc/db_connect.php');

		 			//echo $video_db_name;
		 			//exit;
					$table_name = "videos";

						$column1_name = "sku";
						$column2_name = "v_owner_id";
						$column3_name = "video_db_name";

						$column1_value = "";
						$column2_value = $investor_id;
						$column3_value = $video_db_name;

						$pam1 = "i";
						$pam2 = "s";
						$pam3 = "s";

					include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			        //header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");

		 }

		 if ($status == 1 && $aud_db_name != "") {

		include(ROOT_PATH . 'inc/db_connect.php');

		 			//echo $video_db_name;
		 			//exit;
					$table_name = "aud";

						$column1_name = "sku";
						$column2_name = "aud_owner_id";
						$column3_name = "aud_pic_path";

						$column1_value = "";
						$column2_value = $investor_id;
						$column3_value = $aud_db_name;

						$pam1 = "i";
						$pam2 = "s";
						$pam3 = "s";

					include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			        //header("Location: ../user/index.php?fold=$old_fold&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");

		 }//echo $video_db_name;
$news_inserted_response  = array(
	'news_id' => $news_id, 
	'news_img' => $pic_db_name, 
	'news_aud' => $aud_db_name, 
	'news_vid' => $video_db_name
	);
echo json_encode($news_inserted_response,JSON_UNESCAPED_SLASHES);
//echo $pic_db_name;
