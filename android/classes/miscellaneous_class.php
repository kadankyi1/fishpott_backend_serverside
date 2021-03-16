<?php
class miscellaneousActions {

	function respondFrontEnd1($status, $message) {

		$signUpReturn["data_returned"][0]  = array(
			'status' => $status, 
			'message' => $message

			);
		echo json_encode($signUpReturn); 
		exit;

	} // END OF respondFrontEnd

		function respondFrontEnd2($color, $page, $message) {

		$_SESSION["asem"] = '<span style="color :' .  $color . '">' . $message . '</span>';
		header("Location: $page");
		exit;

	} // END OF respondFrontEnd2

	function respondFrontEnd3($status, $message) {

		$signUpReturn["data_returned"][0]  = array(
			'1' => $status, 
			'2' => $message

			);
		echo json_encode($signUpReturn); 
		exit;

	} // END OF respondFrontEnd



	function getRandomString($length) {
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}// END OF randomString

	function getRandomStringFromDateTime(){
		$t = time();
		$r_t = date("Y-m-d",$t);
		$ext = $r_t . $t;
		return $ext;
	}

	function getCurrencyForUIFromCountry($country){
		$country = trim(strtolower($country));
		if($country == ""){
			return "$";
		} 

		if($country == "ghana"){
			return "₵";
		} else if($country == "united kingdom"){
			return "£";
		} else {
			return "$";
		}
	}// END OF getCurrencyForUIFromCountry

	function getCurrencyForUIFromCurrency($currency){
		$currency = trim($currency);

		if($currency == "₵" || $currency == "£" || $currency == "$"){
			return $currency;
		}

		if($currency == "GHS" || $currency == "GH₵"){
			return "₵";
		} else if($currency == "GBP"){
			return "£";
		} else {
			return "$";
		}
	}// END OF getCurrencyForUIFromCountry

	function getCurrencyForPaymentGatewaysFromCountry($country){
		$country = trim(strtolower($country));
		if($country == ""){
			return "USD";
		} 

		if($country == "ghana"){
			return "GHS";
		} else if($country == "united kingdom"){
			return "GBP";
		} else {
			return "USD";
		}
	}// END OF getCurrencyForPaymentGatewayFromCountry

	function getCurrencyAbreviationsFromSymbols($currency_symbol){
		if($currency_symbol == ""){
			return "USD";
		} 

		if($currency_symbol == "₵" || $currency_symbol == "GHS" || $currency_symbol == "GHc"){
			return "GHS";
		} else if($currency_symbol == "£" || $currency_symbol == "GBP"){
			return "GBP";
		} else {
			return "USD";
		}
	}// END

	function sendNotificationToUser($path_to_fcm, $server_key, $receiver_keys, $sender_profile_picture, $notification_priority, $notification_type, $notification_sub_type, $notification_news_id, $notification_sender_pottname, $notification_title, $notification_body, $notification_date, $alert_type){
		if($receiver_keys[0] != "" || $receiver_keys[1] != "" || $receiver_keys[2] != ""){
			$notification_title = "FishPott - " . $notification_title;
			$headers = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
			$fields = array(
			  "registration_ids" => $receiver_keys,
			  "priority" => $notification_priority,
			  'data' => array(
			    'alert_type' => $alert_type,
			    'notification_type' => $notification_type,
			    'not_type_real' => $notification_sub_type,
			    'not_pic' => $sender_profile_picture,
			    'not_title' => $notification_title,
			    'not_message' => $notification_body,
			    'not_image' => "",
			    'not_video' => "",
			    'not_text' => $notification_body, 
			    'not_pott_or_newsid' => $notification_news_id, 
			    'pott_name' => $notification_sender_pottname, 
			    'not_time' => $notification_date  
			    )
			  );
			$payload = json_encode($fields);
			$curl_session = curl_init();
			curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
			curl_setopt($curl_session, CURLOPT_POST, true);
			curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
			$curl_result = curl_exec($curl_session);

			/*
			echo "\n";
			var_dump($receiver_keys);
			echo "\n";
			var_dump($curl_result);
			*/
			

			return true;
		} else {
			return false;
		}


	} // END OF sendNotificationToUser


	function sendNotificationToTopic($path_to_fcm, $server_key, $topic, $sender_profile_picture, $notification_priority, $notification_type, $notification_sub_type, $notification_news_id, $notification_sender_pottname, $notification_title, $notification_body, $notification_date, $alert_type){
		if($topic != ""){
			$notification_title = "FishPott - " . $notification_title;
			$headers = array('Authorization:key=' . $server_key, 'Content-Type:application/json');
			$fields = array(
			  "to" => '/topics/'.$topic,
			  "priority" => $notification_priority,
			  'data' => array(
			    'alert_type' => $alert_type,
			    'notification_type' => $notification_type,
			    'not_type_real' => $notification_sub_type,
			    'not_pic' => $sender_profile_picture,
			    'not_title' => $notification_title,
			    'not_message' => $notification_body,
			    'not_image' => "",
			    'not_video' => "",
			    'not_text' => $notification_body, 
			    'not_pott_or_newsid' => $notification_news_id, 
			    'pott_name' => $notification_sender_pottname, 
			    'not_time' => $notification_date  
			    )
			  );
			$payload = json_encode($fields);
			$curl_session = curl_init();
			curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
			curl_setopt($curl_session, CURLOPT_POST, true);
			curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
			$curl_result = curl_exec($curl_session);

			/*
			echo "\n";
			var_dump($receiver_keys);
			echo "\n";
			var_dump($curl_result);
			*/
			

			return true;
		} else {
			return false;
		}


	} // END OF sendNotificationToUser


	function getRealIpAddr(){
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {    //check ip from share internet

	      $ip = $_SERVER['HTTP_CLIENT_IP'];

	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy

	      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

	    } else{

	      $ip = $_SERVER['REMOTE_ADDR'];

	    }
	    return $ip;
	}// END OF getRealIpAddr

	function generateIPaddressID($ip_address, $investor_id){

	    return $ip_address . "_" . $investor_id;
	}// END OF getRealIpAddr

	function getServerValueWithKey($SESSION_KEY, $return_type){

		if(isset($_SESSION[$SESSION_KEY])){
			 return trim($_SESSION[$SESSION_KEY]);
		} else {
			if($return_type == "1"){
				return -1;
			} else {
	    		return "";
			}
		}
	}// END OF getServerValueWithKey

	function convertPriceToNewCurrency($old_currency, $old_amount, $new_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $round_up){
		$old_currency = trim($old_currency);
		$new_currency = trim($new_currency);
		if($old_currency == "Ghc" || $old_currency == "GHS" || $old_currency == "₵"){

			if($new_currency == "USD" || $new_currency == "$"){

				if($round_up === true){
					return ceil($GHS_USD * $old_amount);
				} else {
					return $GHS_USD * $old_amount;
				}

			} else if ($new_currency == "GBP" || $new_currency == "£"){

				if($round_up === true){
					return ceil($GHS_GBP * $old_amount);
				} else {
					return $GHS_GBP * $old_amount;
				}

			} else if ($new_currency == "Ghc" || $new_currency == "GHS" || $new_currency == "₵"){
				
				if($round_up === true){
					return ceil($old_amount);
				} else {
					return $old_amount;
				}

			} else {

				return -1;

			}

		} else if ($old_currency == "USD" || $old_currency == "$"){

			if($new_currency == "USD" || $new_currency == "$"){
				
				if($round_up === true){
					return ceil($old_amount);
				} else {
					return $old_amount;
				}

			} else if ($new_currency == "GBP" || $new_currency == "£"){

				if($round_up === true){
					return ceil($USD_GBP * $old_amount);
				} else {
					return $USD_GBP * $old_amount;
				}

			} else if ($new_currency == "Ghc" || $new_currency == "GHS" || $new_currency == "₵"){

				if($round_up === true){
					return ceil($USD_GHS * $old_amount);
				} else {
					return $USD_GHS * $old_amount;
				}

			} else {

				return -1;

			}

			
		} else if ($old_currency == "GBP" || $old_currency == "£"){

			if($new_currency == "USD" || $new_currency == "$"){

				if($round_up === true){
					return ceil($GBP_USD * $old_amount);
				} else {
					return $GBP_USD * $old_amount;
				}

			} else if ($new_currency == "GBP" || $new_currency == "£"){

				if($round_up === true){
					return ceil($old_amount);
				} else {
					return $old_amount;
				}

			} else if ($new_currency == "Ghc" || $new_currency == "GHS" || $new_currency == "₵"){

				if($round_up === true){
					return ceil($GBP_GHS * $old_amount);
				} else {
					return $GBP_GHS * $old_amount;
				}


			} else {

				return -1;

			}
			
		} else {

			return -1;

		}


	}// END OF convertPriceToNewCurrency


	function getAllMentionsAsArrayFromNews($news_text){
		$my_pottname_mentions["mentions"] = array();
		$my_pottname_mentions["mentions_question_marks"] = "";
		$my_pottname_mentions["mentions_value_type_strings"] = "";
		$my_mentions_cnt = 0;
		$my_mentions_question_marks = "";
		$my_mentions_value_type_strings = "";
		preg_match_all("/\B@[a-zA-Z0-9]+/i", $news_text, $mentions);
		$mentions = array_map(function($str){ return substr($str, 1); }, $mentions[0]);
		foreach($mentions as $mentionedUser){
				$my_pottname_mentions["mentions"][$my_mentions_cnt] = strtolower($mentionedUser);
				$my_mentions_question_marks .= "?,";
				$my_mentions_value_type_strings .= "s";
				$pott_mentions_tick = 1;
				$my_mentions_cnt++;
		}

		if(count($my_pottname_mentions["mentions"]) > 0){
			$my_mentions_question_marks = substr($my_mentions_question_marks,0,strlen($my_mentions_question_marks)-1);
			$my_pottname_mentions["mentions_question_marks"] = $my_mentions_question_marks;
		}
		$my_pottname_mentions["mentions_value_type_strings"] = $my_mentions_value_type_strings;

		return $my_pottname_mentions;
	}// END OF getAllMentionsAsArrayFromNews


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
		} else{//maybe you want to check for 200
			return true;
		}
	}

	function getUrlFromNewsText($addNewsText){
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		if(preg_match($reg_exUrl, $addNewsText, $url)) {
		       return $url[0];
		} 
		return "";
	}// END OF getUrlFromNewsText


	function getWebsiteHtmlInfo($full_url){

		$doc = new DOMDocument();
		@$doc->loadHTMLFile($full_url);
		$xpath = new DOMXPath($doc);
		$url_title =  $xpath->query('//title')->item(0)->nodeValue;  
		$tags = $doc->getElementsByTagName('img');
		$current_max_size = 100*100;
		$current_image_link = "";
		foreach ($tags as $tag) {	
		    $image_src = $tag->getAttribute('src');
		    $image_src = trim($image_src);
			if($image_src != ""){
			   if(substr($image_src, 0, 7) != "http://" && substr($image_src, 0, 8) != "https://"){

					$r = parse_url($full_url);
					$image_src = $r["scheme"] . "://" . $r["host"] . "/" . $image_src;

			   }
			   $size_img = getimagesize($image_src);
			   if(($size_img[0] * $size_img[1]) > $current_max_size && ($size_img["mime"] == "image/jpeg" || $size_img["mime"] == "image/jpg" || $size_img["mime"] == "image/png")){

			   		$current_image_link = $image_src;
			   		$current_max_size = $size_img[0] * $size_img[1];
			   }

			}
		}

		return  array('url' => $full_url, 'title' => $url_title, 'img' => $current_image_link);

	}

	function number_format_short( $n, $precision = 1 ) {
		if ($n < 900) {
			// 0 - 900
			$n_format = number_format($n, $precision);
			$suffix = '';
		} else if ($n < 900000) {
			// 0.9k-850k
			$n_format = number_format($n / 1000, $precision);
			$suffix = 'K';
		} else if ($n < 900000000) {
			// 0.9m-850m
			$n_format = number_format($n / 1000000, $precision);
			$suffix = 'M';
		} else if ($n < 900000000000) {
			// 0.9b-850b
			$n_format = number_format($n / 1000000000, $precision);
			$suffix = 'B';
		} else {
			// 0.9t+
			$n_format = number_format($n / 1000000000000, $precision);
			$suffix = 'T';
		}

	  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	  // Intentionally does not affect partials, eg "1.50" -> "1.50"
		if ( $precision > 0 ) {
			$dotzero = '.' . str_repeat( '0', $precision );
			$n_format = str_replace( $dotzero, '', $n_format );
		}

		return $n_format . $suffix;
	}


	function sendEmail($sender_email, $sender_name, $receiver_email, $subject, $message){
        $headers = "From: <" . $sender_email . ">" . $sender_name;
        mail($receiver_email,$subject,$message, $headers);
	}// END OF sendEmail

	function addOrdinalNumberSuffix($num, $return_with_number) {
		$num = intval($num);

	    if (!in_array(($num % 100),array(11,12,13))){
	      switch ($num % 10) {
	        // Handle 1st, 2nd, 3rd
	        case 1:  if($return_with_number){ return $num . 'st'; } else { return 'st'; };
	        case 2:  if($return_with_number){ return $num . 'nd'; } else { return 'nd'; };
	        case 3:  if($return_with_number){ return $num . 'rd'; } else { return 'rd'; };
	      }
	    }

	    if($return_with_number){ return $num . 'th'; } else { return 'th'; };
  }


function sendSMS($action_type, $sms_global_username, $sms_global_password, $from_sender_name, $to_receiver_number, $text_msg){
			
			$post_values = [
			    'action' => $action_type,
			    'user' => $sms_global_username,
			    'password'   => $sms_global_password,
			    'from'   => $from_sender_name,
			    'to'   => $to_receiver_number,
			    'text'   => $text_msg,
			    'api'   => '0'
			];
			

			$ch = curl_init('https://api.smsglobal.com/http-api.php');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_values);

			// execute!
			$response = curl_exec($ch);

			// close the connection, release resources used
			curl_close($ch);

			// do anything you want with your response
			if(substr($response, 0, 2) == "OK"){
				return 1;
			} else {
				return 0;
			}

	}// END OF sendSMS

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
} //gen_uuid


}	