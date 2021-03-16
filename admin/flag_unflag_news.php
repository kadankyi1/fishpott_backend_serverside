<?php
session_start();
$error_page = "../../abanfo/in/examples/_1news_manager.php";

	//CALLING THE CONFIGURATION FILE
	require_once("../android/config.php");

	//CALLING THE INPUT VALIDATOR CLASS
	include_once '../android/classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS
	include_once '../android/classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();
	//CALLING TO THE DATABASE CLASS
	include_once '../android/classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once '../android/classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once '../android/classes/languages_class.php';
	$languagesObject = new languagesActions();


	if(isset($_GET["id"]) && trim($_GET["id"]) != ""){
		$var_news_id = trim($_GET["id"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	
	$error_page = $error_page . "?id=" . $var_news_id;

	if(isset($_GET["t"]) && (intval($_GET["t"]) == 1 || intval($_GET["t"]) == 0)){
		$var_action_type = intval($_GET["t"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	$var_phone = trim($_SESSION["admin_phone"]);
	$input_password_hashed = trim($_SESSION["admin_pass"]);


	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($var_phone, 15) === false){

		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($var_phone) !== true){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($var_phone,1,strlen($var_phone))) !== true){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($var_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT flag, admin_password, admin_id, admin_name, admin_country, admin_profile_pic, admin_level FROM " . ADMIN_BIO_LOGIN_TABLE_NAME . " WHERE admin_phone = ?", 1, "s", array($var_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("flag", "admin_password", "admin_id", "admin_name", "admin_country", "admin_profile_pic", "admin_level"), 7, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//CHECKING THAT THE PASSWORD MATCHES AND THE ACCOUNT IS NOT FLAGGED
	if($input_password_hashed == $prepared_statement_results_array[1] && $prepared_statement_results_array[0] == 0){

		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$var_admin_pass = $prepared_statement_results_array[1];
		$var_admin_id = $prepared_statement_results_array[2];
		$var_admin_level = $prepared_statement_results_array[6];
		$input_language = $prepared_statement_results_array[4];

		if($var_admin_level > ADMIN_LEVEL_2){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- You do not have the clearance level to perform this action. Inform Super Admin");
		}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

	if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
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
	$query =  "SELECT "  
	. USER_BIO_TABLE_NAME . ".fcm_token,  "  
	. USER_BIO_TABLE_NAME . ".fcm_token_ios,  "  
	. USER_BIO_TABLE_NAME . ".debit_wallet_usd,  "  
	. USER_BIO_TABLE_NAME . ".language,  "  
	. USER_BIO_TABLE_NAME . ".country,  "  
	. NEWS_TABLE_NAME . ".inputtor_id FROM "
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. NEWS_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. NEWS_TABLE_NAME . ".inputtor_id INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id "
	. " WHERE " . NEWS_TABLE_NAME . ".news_id = ?";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s",array($var_news_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".fcm_token", 
		USER_BIO_TABLE_NAME . ".fcm_token_ios",
		USER_BIO_TABLE_NAME . ".debit_wallet_usd",
		USER_BIO_TABLE_NAME . ".language",
		USER_BIO_TABLE_NAME . ".country",
		NEWS_TABLE_NAME . ".inputtor_id"
	), 6, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($fcm_token, $fcm_token_ios, $debit_wallet_usd, $user_language, $country, $investor_id);

	$prepared_statement_results_array->fetch();

	if(trim($investor_id) == ""){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(trim($user_language) == ""){
		$user_language = $country;
	}

	$sys_receiver_fcm_token = $fcm_token;
	$sys_receiver_fcm_token_ios = $fcm_token_ios;

	$receiver_keys = array();
	if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
		$receiver_keys[0] = $sys_receiver_fcm_token;
	}

	if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
		$receiver_keys[count($receiver_keys)] = $sys_receiver_fcm_token_ios;
	}



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . NEWS_TABLE_NAME . " SET flag = ? WHERE news_id = ?", 2, "is", array($var_action_type, $var_news_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update flag status. If this continues, inform Super Admin");
	}

	if($var_action_type == 0){
		$sys_status_info = $languagesObject->getLanguageString("news_unflagged", $user_language);
		$sys_status_info2 = $languagesObject->getLanguageString("your_post_is_available_to_be_viwed", $user_language) . " " . $transaction_id;
	} else {
		$sys_status_info = $languagesObject->getLanguageString("news_flagged", $user_language);
		$sys_status_info2 = $languagesObject->getLanguageString("your_post_is_inappropriate", $user_language) . " " . $transaction_id;
	}


	$miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		"fp", 
		"normal", 
		"general_notification", 
		"info", 
		FISHPOT_POTT_ID, 
		FISHPOT_POTT_NAME, 
		$sys_status_info2, 
		$sys_status_info, 
		date("F j, Y"), 
		$transaction_id
	);

	$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT] - Operation successful");

/***********************************************************************************************************************
	
												END PERFORMING NEEDED TASK				

***********************************************************************************************************************/

	} else if($input_password_hashed != $prepared_statement_results_array[1]){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));
	} else if($prepared_statement_results_array[0] != 0){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	} else {
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	
