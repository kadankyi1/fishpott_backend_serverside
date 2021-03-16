<?php
session_start();
$error_page = "../../abanfo/in/examples/_1wallet_view_credit_alerts.php";

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


	if(isset($_GET["i"]) && intval($_GET["i"]) > 0){
		$var_sku = intval($_GET["i"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_GET["t"]) && (intval($_GET["t"]) == 1 || intval($_GET["t"]) == 0)){
		$var_action_type = intval($_GET["t"]);

		if($var_action_type == 1){
			$var_action_type = "completed";
		} else {
			$var_action_type = "cancelled";
		}
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

		if($var_admin_level > SUPER_ADMIN_LEVEL){
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
	. MONEY_CREDIT_TABLE_NAME . ".currency_sent ,  "  
	. MONEY_CREDIT_TABLE_NAME . ".amount_sent,  "    
	. MONEY_CREDIT_TABLE_NAME . ".investor_id ,  "    
	. MONEY_CREDIT_TABLE_NAME . ".transaction_id FROM "
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. MONEY_CREDIT_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. MONEY_CREDIT_TABLE_NAME . ".investor_id INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id "
	. " WHERE " . MONEY_CREDIT_TABLE_NAME . ".done_status = 'pending' AND " . MONEY_CREDIT_TABLE_NAME . ".reviewer_admin_id = '' AND " . LOGIN_TABLE_NAME . ".flag = 0 AND " . MONEY_CREDIT_TABLE_NAME . ".sku = ?";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "i",array($var_sku));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".fcm_token", 
		USER_BIO_TABLE_NAME . ".fcm_token_ios",
		USER_BIO_TABLE_NAME . ".debit_wallet_usd",
		USER_BIO_TABLE_NAME . ".language",
		USER_BIO_TABLE_NAME . ".country",
		MONEY_CREDIT_TABLE_NAME . ".currency_sent",
		MONEY_CREDIT_TABLE_NAME . ".amount_sent",
		MONEY_CREDIT_TABLE_NAME . ".investor_id",
		MONEY_CREDIT_TABLE_NAME . ".transaction_id"
	), 9, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($fcm_token, $fcm_token_ios, $debit_wallet_usd, $user_language, $country, $currency_sent, $amount_sent, $investor_id, $transaction_id);

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



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . MONEY_CREDIT_TABLE_NAME . " SET done_status = ?, reviewer_admin_id = ? WHERE sku = ?", 3, "ssi", array($var_action_type, $var_admin_id, $var_sku));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update transaction status. If this continues, inform Super Admin");
	}

	if($var_action_type == "completed"){
		$amount_sent_usd = $miscellaneousObject->convertPriceToNewCurrency($currency_sent, $amount_sent, "USD", $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$new_debit_wallet_usd = $debit_wallet_usd + $amount_sent_usd;

		
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . USER_BIO_TABLE_NAME . " SET debit_wallet_usd = ? WHERE investor_id = ?", 2, "ds", array($new_debit_wallet_usd, $investor_id));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to credit user. Inform Super Admin to do this manually");
		}

		$sys_status_info = $languagesObject->getLanguageString("credit_completed", $user_language);
		$sys_status_info2 = $languagesObject->getLanguageString("your_credit_request_has_been_completed", $user_language) . " " . $transaction_id;
	} else {
		$sys_status_info = $languagesObject->getLanguageString("credit_cancelled", $user_language);
		$sys_status_info2 = $languagesObject->getLanguageString("your_credit_request_was_cancelled", $user_language) . " " . $transaction_id;
	}


	$miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		"fp", 
		"normal", 
		"general_notification", 
		"credit", 
		FISHPOT_POTT_ID, 
		FISHPOT_POTT_NAME, 
		$sys_status_info, 
		$sys_status_info2, 
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
	
