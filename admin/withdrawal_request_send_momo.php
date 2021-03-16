<?php
session_start();
$error_page = "../../abanfo/in/examples/_1withdrawal_requests.php";

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

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../android/classes/mtn_mobile_money_class.php';
	$mtnMomoObject = new mtnMomoActions();


	if(isset($_GET["i"]) && intval($_GET["i"]) > 0){
		$var_sku = intval($_GET["i"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_GET["t"]) && (intval($_GET["t"])) == 1){
		$var_action_type = "completed";
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
	. USER_BIO_TABLE_NAME . ".country ,  "  
	. WITHDRAWAL_TABLE_NAME . ".amount,  "  
	. WITHDRAWAL_TABLE_NAME . ".pay_currency ,  "  
	. USER_BIO_TABLE_NAME . ".investor_id,  "  
	. USER_BIO_TABLE_NAME . ".pot_name, "
	. WITHDRAWAL_TABLE_NAME . ".account_number, "
	. WITHDRAWAL_TABLE_NAME . ".amount FROM "
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. WITHDRAWAL_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. WITHDRAWAL_TABLE_NAME . ".investor_id INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id "
	. " WHERE " . WITHDRAWAL_TABLE_NAME . ".paid_status = 'pending' AND " . WITHDRAWAL_TABLE_NAME . ".action_admin = '' AND " . LOGIN_TABLE_NAME . ".flag = 0 AND " . WITHDRAWAL_TABLE_NAME . ".sku = ?";


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
		WITHDRAWAL_TABLE_NAME . ".amount",
		WITHDRAWAL_TABLE_NAME . ".pay_currency",
		USER_BIO_TABLE_NAME . ".investor_id",
		USER_BIO_TABLE_NAME . ".pot_name",
		USER_BIO_TABLE_NAME . ".account_number",
		USER_BIO_TABLE_NAME . ".amount"
	), 11, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($fcm_token, $fcm_token_ios, $debit_wallet_usd, $user_language, $country, $amount, $pay_currency, $investor_id, $input_mypottname, $input_mtn_phone_number, $input_amount);

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



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . WITHDRAWAL_TABLE_NAME . " SET paid_status = ?, action_admin = ?, paid_date = ? WHERE sku = ?", 4, "sssi", array($var_action_type, $var_admin_id, date("Y-m-d H:i:s"), $var_sku));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update transaction status. If this continues, inform Super Admin");
	}

	/////////////////////////////////////////////// SENDING MOMO START //////////////////////////////////////////////
	
	$sys_callback_url = "";

	$sys_mtn_api_user = $mtnMomoObject->mtnCreateApiUser($miscellaneousObject->gen_uuid(), MTN_MOMO_DISBURSEMENT_API_KEY_1, MTN_CREATE_API_USER_URL, $sys_callback_url);

	if(strlen($sys_mtn_api_user) != 36){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- FAILED TO CREATE MTN MOMO API USER BUT UPDATED WITDHRAWAL REQUEST AS PAID, inform Super Admin");
	}

	$api_url = MTN_CREATE_API_USER_URL . "/" . $sys_mtn_api_user . "/apikey";

	$sys_mtn_api_key = $mtnMomoObject->mtnGetApiKey($sys_mtn_api_user, MTN_MOMO_DISBURSEMENT_API_KEY_1, $api_url);

	if($sys_mtn_api_key == "0"){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- FAILED TO CREATE MTN MOMO API KEY BUT UPDATED WITDHRAWAL REQUEST AS PAID, inform Super Admin");
	}

	$sys_mtn_api_token = $mtnMomoObject->mtnGetApiToken($sys_mtn_api_user, $sys_mtn_api_key, MTN_MOMO_DISBURSEMENT_API_KEY_1, MTN_CREATE_DISBURSEMENT_TOKEN_URL);

	if($sys_mtn_api_token == "0"){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- FAILED TO CREATE MTN MOMO API TOKEN BUT UPDATED WITDHRAWAL REQUEST AS PAID, inform Super Admin");
	}


	//$sys_callback_url = HTTP_HEAD_FOR_FISHPOTT . "/inc/android/mtn_momo/mtn_mobile_money_collection_request_response.php?phone=" . $input_phone . "&pn=" . $input_mypottname . "&reqphone=" . $input_mtn_phone_number . "&curr=" . $pay_currency . "&amt=" . $input_amount . "&datetime=" . date("Y-m-d_H:i:s") . "&lang=" . $input_language;

	$sys_currency_symbol = $miscellaneousObject->getCurrencyForUIFromCurrency($pay_currency);
	$sys_currency_abbreviation = $miscellaneousObject->getCurrencyAbreviationsFromSymbols($sys_currency_symbol);


	$sys_payer_message = "FishPott Withdrawal Request of " . $sys_currency_abbreviation .  $input_amount . " for @" . $input_mypottname;

	/*
	echo "<BR><BR> sys_mtn_api_token : " . $sys_mtn_api_token . "<BR><BR>";
	echo "<BR><BR> sys_callback_url : " . $sys_callback_url . " <BR><BR>" ;
	echo "<BR><BR> MTN_CURRENT_TARGET_ENVIRONMENT : " . MTN_CURRENT_TARGET_ENVIRONMENT . "<BR><BR>";
	echo "<BR><BR> MTN_MOMO_DISBURSEMENT_API_KEY_1 : " . MTN_MOMO_DISBURSEMENT_API_KEY_1 . "<BR><BR>";
	echo "<BR><BR> MTN_CREATE_DISBURSEMENT_SEND_TRANSFER_URL : " . MTN_CREATE_DISBURSEMENT_SEND_TRANSFER_URL . " <BR><BR>" ;
	echo "<BR><BR> input_amount : " . $input_amount . " <BR><BR>" ;
	echo "<BR><BR> input_mypottname : " . $input_mypottname . " <BR><BR>" ;
	echo "<BR><BR> input_mtn_phone_number : " . $input_mtn_phone_number . " <BR><BR>" ;
	echo "<BR><BR> sys_payer_message : " . $sys_payer_message . " <BR><BR>" ;
	*/

	$sys_mtn_requesttopay_response = $mtnMomoObject->mtnSendMomoTransferRequest($sys_mtn_api_token, $sys_callback_url, MTN_CURRENT_TARGET_ENVIRONMENT, $miscellaneousObject->gen_uuid(), MTN_MOMO_DISBURSEMENT_API_KEY_1,  MTN_CREATE_DISBURSEMENT_SEND_TRANSFER_URL, $input_amount, "EUR", $input_mypottname, "MSISDN", $input_mtn_phone_number, $sys_payer_message);

	//echo "<BR><BR> sys_mtn_requesttopay_response : " . $sys_mtn_requesttopay_response . " <BR><BR>" ;

	if($sys_mtn_requesttopay_response != "1"){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- FAILED TO SEND MTN MOMO TRANSFER BUT UPDATED WITDHRAWAL REQUEST AS PAID, inform Super Admin");
	}

	/////////////////////////////////////////////// SENDING MOMO END //////////////////////////////////////////////



	if($var_action_type == "completed"){
		$sys_status_info = $languagesObject->getLanguageString("withdrawal_request_paid", $user_language);
		$sys_status_info2 = $pay_currency . " " . $amount . " " . $languagesObject->getLanguageString("withdrawal_request_paid", $user_language);
	} else {

		$sys_status_info = $languagesObject->getLanguageString("withdrawal_request_rejected", $user_language);
		$sys_status_info2 =  $pay_currency ." " . $amount . " " . $languagesObject->getLanguageString("withdrawal_request_rejected", $user_language);
	}


	$miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		"fp", 
		"normal", 
		"general_notification", 
		"withdrawal", 
		FISHPOT_POTT_ID, 
		FISHPOT_POTT_NAME, 
		$sys_status_info, 
		$sys_status_info2, 
		date("F j, Y"), 
		""
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
	
