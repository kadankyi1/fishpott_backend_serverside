<?php
exit
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( Isset($_GET["phone"]) && trim($_GET["phone"]) != "" &&
	isset($_GET["pn"]) && trim($_GET["pn"]) != "" &&
	isset($_GET["reqphone"]) && trim($_GET["reqphone"]) != "" &&
	isset($_GET["curr"]) && trim($_GET["curr"]) != "" &&
	isset($_GET["amt"]) && trim($_GET["amt"]) != "" &&
	isset($_GET["datetime"]) && trim($_GET["datetime"]) != "" && 
	isset($_GET["lang"]) && trim($_GET["lang"]) != "") {

	//CALLING THE CONFIGURATION FILE
	require_once("../config.php");

	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_GET["pn"]) && trim($_GET["pn"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_POTTNAME);
		if (in_array(trim($_GET["mypottname"]), $ALL_DEVELOPER_POTTNAMES)){
			$GLOBALS["USAGE_MODE_IS_LIVE"] = DEVELOPER_USING_LIVE_MODE;
		}
	}


	//CALLING THE INPUT VALIDATOR CLASS & CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	include_once '../classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();

	//CALLING TO THE DATABASE CLASS & CREATING DATABASE CONNECTION OBJECT
	include_once '../classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS && CREATING PREPARED STATEMENT QUERY OBJECT
	include_once '../classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS & CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	include_once '../classes/languages_class.php';
	$languagesObject = new languagesActions();

	//CALLING TO THE COUNTRY CODES CLASS & CREATING COUNTRY CODES OBJECT
	include_once '../classes/country_codes_class.php';
	$countryCodesObject = new countryCodes();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../classes/file_class.php';
	$fileObject = new fileActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../classes/news_class.php';
	$newsObject = new newsActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../classes/time_class.php';
	$timeObject = new timeOperator();
	$newsObject = new newsActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once '../classes/mtn_mobile_money_class.php';
	$mtnMomoObject = new mtnMomoActions();


	$input_phone = trim($_GET["phone"]);
	$input_mypottname = trim($_GET["pn"]);
	$input_my_currency = trim($_GET["curr"]);
	$input_pay_type = "Mobile Money";
	$input_transaction_id = uniqid() . "_" . trim($_GET["reqphone"]);
	$input_amount_sent = floatval($_GET["amt"]);
	$input_sender_name = trim($_GET["reqphone"]);
	$input_send_date = trim($_GET["datetime"]);
	$input_language = trim($_GET["lang"]);

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_currency_symbol = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency);
	$sys_currency_abbreviation = $miscellaneousObject->getCurrencyAbreviationsFromSymbols($sys_currency_symbol);


	// GETTING USER'S INFO
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT debit_wallet_usd, withdrawal_wallet_usd, fcm_token, fcm_token_ios, net_worth, investor_id FROM " . USER_BIO_TABLE_NAME . " WHERE pot_name = ?", 1, "s", array($input_mypottname));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("debit_wallet_usd", "withdrawal_wallet_usd", "fcm_token", "fcm_token_ios", "net_worth", "investor_id"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if(    trim($prepared_statement_results_array[2]) == "fcm_token"
		|| trim($prepared_statement_results_array[3]) == "fcm_token_ios"
	){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_debit_wallet_usd = floatval($prepared_statement_results_array[0]);
	$sys_withdrawal_wallet_usd = floatval($prepared_statement_results_array[1]);
	$sys_pott_pearls = $miscellaneousObject->number_format_short(intval($prepared_statement_results_array[4]), 1);
	$sys_receiver_fcm_token = $prepared_statement_results_array[2];
	$sys_receiver_fcm_token_ios = $prepared_statement_results_array[3];
	$input_id = $prepared_statement_results_array[5];

	$receiver_keys = array();
	if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
		$receiver_keys[0] = $sys_receiver_fcm_token;
	}
	if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
		$receiver_keys[count($receiver_keys)] = $sys_receiver_fcm_token_ios;
	}

	/*
	echo "\n\n sys_debit_wallet_usd : " . $sys_debit_wallet_usd . "\n\n";
	echo "\n\n sys_withdrawal_wallet_usd : " . $sys_withdrawal_wallet_usd . "\n\n";
	echo "\n\n sys_pott_pearls : " . $sys_pott_pearls . "\n\n";
	echo "\n\n sys_receiver_fcm_token : " . $sys_receiver_fcm_token . "\n\n";
	echo "\n\n sys_receiver_fcm_token_ios : " . $sys_receiver_fcm_token_ios . "\n\n";
	echo "\n\n KEYS \n\n";
	var_dump($receiver_keys);
	*/


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . MONEY_CREDIT_TABLE_NAME . " (
			done_status,
			pay_type,
	 		currency_sent,
	 		amount_sent,
	 		transaction_id, 
	 		sender_name, 
	 		investor_id, 
	 		send_date, 
	 		input_date
	 	) 
	 		VALUES 
		(?, ?, ?, ?, ?, ?, ?, ?, ?)" ,
		9, 
		"sssdsssss", 
	    array(
	    	"pending",
		  	$input_pay_type, 
		  	$sys_currency_abbreviation, 
		  	$input_amount_sent, 
		  	$input_transaction_id, 
		  	$input_sender_name, 
		  	$input_id, 
		  	$input_send_date, 
		  	date("Y-m-d H:i:s")
		)
	);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_status_info = $languagesObject->getLanguageString("credit_request_sent_for", $input_language) . " " . $sys_currency_symbol . $input_amount_sent;

	//NOTIFYING RECEIVER
	$miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		"fp", 
		"normal", 
		"general_notification", 
		"credit_request", 
		$input_id, 
		$input_mypottname, 
		$languagesObject->getLanguageString("credit_request_sent", $input_language), 
		$sys_status_info, 
		date("F j, Y"), 
		""
	);


}