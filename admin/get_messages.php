<?php
session_start();

	//CALLING THE CONFIGURATION FILE
	require_once("../android/config.php");

	//CALLING THE INPUT VALIDATOR CLASS
	include_once '../android/classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS
	include_once '../android/classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once '../android/classes/time_class.php';
	$timeObject = new timeOperator();

	//CALLING TO THE DATABASE CLASS
	include_once '../android/classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once '../android/classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once '../android/classes/languages_class.php';
	$languagesObject = new languagesActions();

	if(isset($_POST["last_sku"]) && intval($_POST["last_sku"]) > 0){
		$last_sku = intval($_POST["last_sku"]);
	} else {
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}


	if(isset($_POST["id_1"]) && trim($_POST["id_1"]) != ""){
		$id_1 = trim($_POST["id_1"]);
	} else {
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if(isset($_POST["id_2"]) && trim($_POST["id_2"]) != ""){
		$id_2 = trim($_POST["id_2"]);
	} else {
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($id_1 != FISHPOT_POTT_NAME){
		$input_receiver_pottname = $id_1;
	} else if($id_2 != FISHPOT_POTT_NAME){
		$input_receiver_pottname = $id_2;
	} else {
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sysResponse["news_returned"] = array();
	$var_phone = trim($_SESSION["admin_phone"]);
	$input_password_hashed = trim($_SESSION["admin_pass"]);


	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($var_phone, 15) === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($var_phone) !== true){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($var_phone,1,strlen($var_phone))) !== true){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($var_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT flag, admin_password, admin_id, admin_name, admin_country, admin_profile_pic, admin_level FROM " . ADMIN_BIO_LOGIN_TABLE_NAME . " WHERE admin_phone = ?", 1, "s", array($var_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("flag", "admin_password", "admin_id", "admin_name", "admin_country", "admin_profile_pic", "admin_level"), 7, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//CHECKING THAT THE PASSWORD MATCHES AND THE ACCOUNT IS NOT FLAGGED
	if($input_password_hashed == $prepared_statement_results_array[1] && $prepared_statement_results_array[0] == 0){

		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$var_admin_pass = $prepared_statement_results_array[1];
		$var_admin_id = $prepared_statement_results_array[2];
		$var_admin_level = $prepared_statement_results_array[6];
		$input_language = $prepared_statement_results_array[4];

		if($var_admin_level > ADMIN_LEVEL_2){
			$miscellaneousObject->respondFrontEnd3(2, "[NT]- You do not have the clearance level to perform this action. Inform Super Admin");
		}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$GHS_USD = $prepared_statement_results_array[0];
	$USD_GHS = $prepared_statement_results_array[1];
	$GHS_GBP = $prepared_statement_results_array[2];
	$GBP_GHS = $prepared_statement_results_array[3];
	$USD_GBP = $prepared_statement_results_array[4];
	$GBP_USD = $prepared_statement_results_array[5];


/***********************************************************************************************************************
	
												START PERFORMING NEEDED TASK				

***********************************************************************************************************************/

		// GETTING THE NEWS CONTENT
	$news_fetch_query =  "SELECT  sku, sender_pottname, receiver_pottname, message_text, message_time, chat_id FROM " . CHAT_MESSAGES_TABLE_NAME . " WHERE ((sender_pottname = ? AND receiver_pottname = ?) OR (receiver_pottname = ? AND sender_pottname = ?))  AND sku > ? ORDER BY  " . CHAT_MESSAGES_TABLE_NAME . ".sku DESC";



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $news_fetch_query, 5, "ssssi",array($id_1, $id_2, $id_1, $id_2, $last_sku));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		CHAT_MESSAGES_TABLE_NAME . ".sku",
		CHAT_MESSAGES_TABLE_NAME . ".sender_pottname",
		CHAT_MESSAGES_TABLE_NAME . ".receiver_pottname",
		CHAT_MESSAGES_TABLE_NAME . ".message_text",
		CHAT_MESSAGES_TABLE_NAME . ".message_time",
		CHAT_MESSAGES_TABLE_NAME . ".chat_id"
	), 6, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($sku, $sender_pottname, $receiver_pottname, $message_text, $message_time, $chat_id);

	while($prepared_statement_results_array->fetch()){

		$message_id = strval($sku) . "_" . $chat_id;

			$next  = array(				
				"0a" => $message_id,
				"1" => $chat_id,
				"2" => $sender_pottname,
				"3" => $receiver_pottname, 
				"4" => $message_text,
				"5" => $timeObject->reformatDate("M j, Y, g:i a", $message_time), 
				"6" => $sku
				);
			array_push($sysResponse["news_returned"], $next);
	}

	 //var_dump($json_response);
	 //echo "here 999 \n";
	 echo safe_json_encode($sysResponse); 


/***********************************************************************************************************************
	
												END PERFORMING NEEDED TASK				

***********************************************************************************************************************/

	} else if($input_password_hashed != $prepared_statement_results_array[1]){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));
	} else if($prepared_statement_results_array[0] != 0){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	} else {
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	


function safe_json_encode($value){
	if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
	    $encoded = json_encode($value, JSON_PRETTY_PRINT);
	} else {
	    $encoded = json_encode($value);
	}
	switch (json_last_error()) {
	    case JSON_ERROR_NONE:
	        return $encoded;
	    case JSON_ERROR_DEPTH:
	        return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_STATE_MISMATCH:
	        return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_CTRL_CHAR:
	        return 'Unexpected control character found';
	    case JSON_ERROR_SYNTAX:
	        return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_UTF8:
	        $clean = utf8ize($value);
	        return safe_json_encode($clean);
	    default:
	        return 'Unknown error'; // or trigger_error() or throw new 
	Exception();
	}
}


function utf8ize($mixed) {
	if (is_array($mixed)) {
	    foreach ($mixed as $key => $value) {
	        $mixed[$key] = utf8ize($value);
	    }
	} else if (is_string ($mixed)) {
	    return utf8_encode($mixed);
	}
	return $mixed;
}
