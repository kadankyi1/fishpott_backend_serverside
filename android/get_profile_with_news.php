<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["viewing_pottname"]) && trim($_POST["viewing_pottname"]) != "" &&
	isset($_POST["show_bio"]) && trim($_POST["show_bio"]) != "" &&
	isset($_POST["show_shares"]) && trim($_POST["show_shares"]) != "" &&
	isset($_POST["lastest_sku"]) && trim($_POST["lastest_sku"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_POTTNAME);
		if (in_array(trim($_POST["mypottname"]), $ALL_DEVELOPER_POTTNAMES)){
			$GLOBALS["USAGE_MODE_IS_LIVE"] = DEVELOPER_USING_LIVE_MODE;
		}
	}

	

	//CALLING THE INPUT VALIDATOR CLASS & CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	include_once 'classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();

	//CALLING TO THE DATABASE CLASS & CREATING DATABASE CONNECTION OBJECT
	include_once 'classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS && CREATING PREPARED STATEMENT QUERY OBJECT
	include_once 'classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS & CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	include_once 'classes/languages_class.php';
	$languagesObject = new languagesActions();

	//CALLING TO THE COUNTRY CODES CLASS & CREATING COUNTRY CODES OBJECT
	include_once 'classes/country_codes_class.php';
	$countryCodesObject = new countryCodes();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/file_class.php';
	$fileObject = new fileActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/news_class.php';
	$newsObject = new newsActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/time_class.php';
	$timeObject = new timeOperator();


	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["log_phone"]);
	$input_pass = trim($_POST["log_pass_token"]);
	$input_mypottname = trim($_POST["mypottname"]);
	$input_my_currency = trim($_POST["my_currency"]);
	$input_viewing_pottname = trim($_POST["viewing_pottname"]);
	$input_show_bio = intval($_POST["show_bio"]);
	$input_show_shares = intval($_POST["show_shares"]);
	$input_lastest_sku = intval($_POST["lastest_sku"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);



	//DECLARING THE ARRAY FOR THE RESULTS
	$temp_sysResponse_1["news_returned"] = array();
	$sysResponse["shares_returned"] = array();

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS IS SET TO FALSE
	$government_id_verification_is_on = false;

	// MAKING SURE THE APP THE PERSON IS USING IS AN ALLOWED VERSION
	if($input_app_version_code < MINIMUM_ALLOWED_VERSION_CODE){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THE PHONE MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("login_failed_if_this_continues_uninstall_your_app_reinstall_and_login_again", $input_language));
	}

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_mypottname) !== true){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("login_failed_if_this_continues_uninstall_your_app_reinstall_and_login_again", $input_language));
	}

	// GETTING THE CURRENCY SYMBOL OF THE USER
	$sys_currency_symbol = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency);

	// CHECKING IF THE DATABASE CONNECTION WAS SUCCESSFUL
	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, government_id_verified, request_government_id, id FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "government_id_verified", "request_government_id", "id"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if(    trim($prepared_statement_results_array[0]) == "password"
		|| trim($prepared_statement_results_array[5]) == "id" 
	){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}


	// SETTING USER ID
	$input_id = trim($prepared_statement_results_array[5]);

	//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
	if(FORCE_GOVERNMENT_STATUS || ($prepared_statement_results_array[3] == 0 && $prepared_statement_results_array[4] == 1)){
		$government_id_verification_is_on = true;
	} else {
		$government_id_verification_is_on = false;
	}

	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){
		$miscellaneousObject->respondFrontEnd3(4, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	}

	//CHECKING IF THE INPUT PASSWORD MATCHES THE DATABASE PASSWORD OTHERWISE WE FAIL THE REQUEST
	if($prepared_statement_results_array[0] != $input_pass){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("session_closed_restart_the_app_and_login_to_start_a_new_session", $input_language));
	}

	// CHECKING IF PHONE VERIFICATION IS ON, AND WHEN CHECKING
	// IF USER ACCOUNT IS PENDING SMS VERIFICATION
	if($prepared_statement_results_array[1] == -1){
		$phone_verification_is_on = true;
	} else if($prepared_statement_results_array[1] == 0 && LOGIN_PHONE_NUMBER_VERIFICATION_IS_ON === true){
		$phone_verification_is_on = true;
		$reset_code = $miscellaneousObject->getRandomString(9);
/*****************************************************************************************************************
			

		SEND VERIFICATION SMS HERE. MAKE SURE THERE IS NO DATE IN THE DATABASE OR THE DATE IS PAST 24 HOURS


******************************************************************************************************************/
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET number_verified = ?, number_verification_code = ?, last_sms_sent_datetime = ?  WHERE number_login = ?", 4, "isss", array( -1, $reset_code, date("Y-m-d H:i:s"), $input_phone));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

	} else {
		$phone_verification_is_on = false;
	}

/*****************************************************************************************************************
			

			SAVING USER'S IP ADDRESS COUNT


******************************************************************************************************************/
	// GETTING USER'S IP ADDRESS
	$ip_address = $miscellaneousObject->getRealIpAddr();
	$ip_address_id = $miscellaneousObject->generateIPaddressID($ip_address, $input_id);


	if($ip_address != ""){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT ip_usage_count FROM " . IP_ADDRESSES_TABLE_NAME . " WHERE ip_id = ?", 1, "s", array($ip_address_id));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("ip_usage_count"), 1, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// IF THE DATABASE QUERY GOT NO RESULTS
		if($prepared_statement_results_array[0] <= 0){
			//INSERTING NEW IP ADDRESS USAGE
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . IP_ADDRESSES_TABLE_NAME . " (ip_id, investor_id, ip_address, ip_usage_count) VALUES (?, ?, ?, ?)" , 4, "sssi", array($ip_address_id, $input_id, $ip_address, 1));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}

		} else {
			// UPDATING IP USAGE COUNT
			$new_ip_count = $prepared_statement_results_array[0] + 1;
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . IP_ADDRESSES_TABLE_NAME . " SET ip_usage_count = ? WHERE ip_id = ?", 2, "is", array( $new_ip_count, $ip_address_id));
			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}
		}

	} else {
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

/***********************************************************************************************************

							GETTING CURRENCY EXCHANGE RATES

***********************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD, coins_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD", "coins_USD"), 7, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$GHS_USD = $prepared_statement_results_array[0];
	$USD_GHS = $prepared_statement_results_array[1];
	$GHS_GBP = $prepared_statement_results_array[2];
	$GBP_GHS = $prepared_statement_results_array[3];
	$USD_GBP = $prepared_statement_results_array[4];
	$GBP_USD = $prepared_statement_results_array[5];
	$coins_USD = $prepared_statement_results_array[6];

	$transfer_fee = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $miscellaneousObject->convertPriceToNewCurrency("USD", FISHPOTT_TRANSFER_FEE_IN_DOLLARS, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

	//UPDATING THE LAST SEEN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

	$news_fetch_query =  "SELECT "  
	. USER_BIO_TABLE_NAME . ".first_name,  " 
	. USER_BIO_TABLE_NAME . ".last_name,  " 
	. USER_BIO_TABLE_NAME . ".investor_id,  " 
	. USER_BIO_TABLE_NAME . ".country,  " 
	. USER_BIO_TABLE_NAME . ".coins_secure_datetime,  " 
	. USER_BIO_TABLE_NAME . ".verified_tag,  "
	. USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd,  "
	. USER_BIO_TABLE_NAME . ".debit_wallet_usd,  "
	. USER_BIO_TABLE_NAME . ".net_worth,  "
	. USER_BIO_TABLE_NAME . ".profile_picture,  "
	. LOGIN_TABLE_NAME . ".login_type ,  "
	. USER_BIO_TABLE_NAME . ".referred_by ,  "
	. USER_BIO_TABLE_NAME . ".poach_ruled_by,  "
	. USER_BIO_TABLE_NAME . ".rank FROM " 
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id "
	. " WHERE " . LOGIN_TABLE_NAME . ".flag = 0  AND " . USER_BIO_TABLE_NAME . ".pot_name = ?";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($input_viewing_pottname));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".first_name", 
		USER_BIO_TABLE_NAME . ".last_name", 
		USER_BIO_TABLE_NAME . ".investor_id", 
		USER_BIO_TABLE_NAME . ".country", 
		USER_BIO_TABLE_NAME . ".coins_secure_datetime", 
		USER_BIO_TABLE_NAME . ".verified_tag", 
		USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd", 
		USER_BIO_TABLE_NAME . ".debit_wallet_usd", 
		USER_BIO_TABLE_NAME . ".net_worth", 
		USER_BIO_TABLE_NAME . ".profile_picture", 
		LOGIN_TABLE_NAME . ".login_type", 
		USER_BIO_TABLE_NAME . ".referred_by", 
		USER_BIO_TABLE_NAME . ".poach_ruled_by",
		USER_BIO_TABLE_NAME . ".rank"
	), 14, 1);

	if(
		   $prepared_statement_results_array === false 
		|| $prepared_statement_results_array[0] == USER_BIO_TABLE_NAME . ".first_name"
		|| $prepared_statement_results_array[0] == "first_name"
	){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("pott_not_found", $input_language)); exit;
	}

	$db_full_name = $prepared_statement_results_array[0] . " " . $prepared_statement_results_array[1];
	$db_investor_id = $prepared_statement_results_array[2];
	$db_country = $prepared_statement_results_array[3];
	$db_last_seen = $prepared_statement_results_array[4];
	$db_verified_status = intval($prepared_statement_results_array[5]);
	$db_wallet = floatval($prepared_statement_results_array[6]) + floatval($prepared_statement_results_array[7]);
	$db_pearls = $miscellaneousObject->number_format_short(intval($prepared_statement_results_array[8]), 1);
	$db_profile_picture = "../../pic_upload/" . $prepared_statement_results_array[9];
	$db_login_type = $prepared_statement_results_array[10];

	if($prepared_statement_results_array[12] != ""){
		$db_ruler = $prepared_statement_results_array[12];
	} else {
		$db_ruler = $prepared_statement_results_array[11];
	}

	$db_rank = intval($prepared_statement_results_array[13]);


	if($prepared_statement_results_array[8] != 0){
		$db_pearls_value = $coins_USD * $prepared_statement_results_array[8];
	}

	if($prepared_statement_results_array[9] != "" && $validatorObject->fileExists($db_profile_picture) !== false){
		$db_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $prepared_statement_results_array[9];
	} else {
		$db_profile_picture = "";
	}

	$db_last_seen = $timeObject->reformatDate("j M, y", $db_last_seen);

	if($db_login_type == "investor"){
		$db_login_type = 1;
	} else if($db_login_type == "business"){
		$db_login_type = 2;
	} else {
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("pott_not_found", $input_language)); exit;
	}

	/*
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . LOGIN_TABLE_NAME . " WHERE flag = 0 ", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("COUNT(*)"), 1, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($prepared_statement_results_array[0] == "COUNT(*)" || $prepared_statement_results_array[0] == 0){
		$db_total_users = "";
	} else {
		$db_total_users = $miscellaneousObject->number_format_short(intval($prepared_statement_results_array[0]), 1);
	}
	*/

	if($db_rank <= 0){
		$db_rank_info = $languagesObject->getLanguageString("this_pott_is_not_yet_ranked", $input_language);
	} else {

		$db_rank_info = $languagesObject->getLanguageString("rank_of_wealth", $input_language) . ": " .   $miscellaneousObject->addOrdinalNumberSuffix($db_rank, true);
	}

	// GETTING FOLLOWERS
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . LINKUPS_TABLE_NAME . " WHERE receiver_id = ? ", 1, "s", array($db_investor_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("COUNT(*)"), 1, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($prepared_statement_results_array[0] == "COUNT(*)" || $prepared_statement_results_array[0] == 0){
		$db_followers = "0";
	} else {
		$db_followers = $miscellaneousObject->number_format_short(intval($prepared_statement_results_array[0]), 1);
	}

	// POTT WORTH - TOTAL VALUE OF SHARES OWNED + PEARLS * RATE + 
	$news_fetch_query =  "SELECT "  
	. " SUM(" . SHARES_HOSTED_TABLE_NAME . ".value_per_share * " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares) AS total, " 
	. " SUM(" . SHARES_HOSTED_TABLE_NAME . ".yield_per_share * " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares) AS yield FROM " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id="  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id"
	. " WHERE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ?  AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".flag = 0 AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($db_investor_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		"total", "yield"
	), 2, 1);

	if(
		   $prepared_statement_results_array === false 
		|| $prepared_statement_results_array[0] == "total"
	){
		$db_total_value_of_shares_owned = 0;
		$db_yield_value_of_shares_owned = 0;
	} else {
		$db_total_value_of_shares_owned = $prepared_statement_results_array[0];
		$db_yield_value_of_shares_owned = $prepared_statement_results_array[1];
	}

	$db_total_value_of_pott = intval($db_pearls_value + $db_total_value_of_shares_owned);

	if($db_yield_value_of_shares_owned > 0){
		$db_yield_value_of_shares_owned = $languagesObject->getLanguageString("This_pott_makes_yearly_returns_of", $input_language) . " $" . $miscellaneousObject->number_format_short(intval($db_yield_value_of_shares_owned, 1));
	} else {
		$db_yield_value_of_shares_owned = $languagesObject->getLanguageString("This_pott_makes_no_dividends", $input_language);

	}

	$db_total_value_of_pott = "$" . $miscellaneousObject->number_format_short(intval($db_total_value_of_pott, 1));


	// GETTING THE NEWS CONTENT
	$news_fetch_query =  "SELECT  chat_id FROM " . CHAT_MESSAGES_TABLE_NAME . " WHERE (sender_pottname = ? AND receiver_pottname = ?) OR (receiver_pottname = ? AND sender_pottname = ?) ORDER BY sku DESC";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 4, "ssss",array($input_mypottname, $input_viewing_pottname, $input_mypottname, $input_viewing_pottname));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		"chat_id"
	), 1, 1);

	if(
		   $prepared_statement_results_array === false 
		|| $prepared_statement_results_array[0] == "chat_id"
		|| $prepared_statement_results_array[0] == ""
	){
		$db_chat_id = "s_" . $input_id . "_r_" . $db_investor_id;
	} else {
		$db_chat_id = trim($prepared_statement_results_array[0]);
	}

	//CHECKING IF A LINKUP ALREADY EXISTS
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT status FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$db_investor_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("status"), 1, 1);

	if(
		   $prepared_statement_results_array === false 
		|| $prepared_statement_results_array[0] == "status"
		|| $prepared_statement_results_array[0] == 0
	){
		$db_linkup_status = 0;
	} else {
		$db_linkup_status = intval($prepared_statement_results_array[0]);
	}


	if($input_show_bio == 1){
		$bio_array  = array(
			"0a" => 0,
			"1" => 32, 
			"2" => $db_chat_id,
			"3" => $db_country, 
			"4" => $db_last_seen,  
			"5" => $db_pearls, 
			"6" => $db_followers, 
			"7" => $db_total_value_of_pott, 
			"8" => $db_yield_value_of_shares_owned, 
			"9" => $db_rank_info,
			"10" => $db_ruler,
			"11" => $db_investor_id, 
			"12" =>"",
			"13" => $db_full_name, 
			"14" => $db_profile_picture, 
			"15" => $db_linkup_status,
			"16" =>"",
			"17" =>"",
			"18" =>"",
			"19" =>"",
			"20" => $db_login_type,
			"21" => $db_verified_status,
			"22" =>"",
			"23" =>"",
			"24" =>0,
			"25" =>0,
			"26" =>0,
			"27" =>"",
			"28" =>"",
			"29" =>"",
			"30" =>"",
			"31" =>"",
			"32" =>"",
			"33" =>"",
			"34" =>0,
			"35" =>"",
			"36" =>"",
			"37" =>"",
			"38" =>"",
			"39" =>"",
			"40" =>"",
			"41" =>"",
			"42" =>"",
			"43" =>0,
			"44" =>"",
			"45" =>0,
			"46" =>0,
			"47" =>"",
			"48" =>"",
			"49" =>"",
			"50" =>"",
			"51" =>"",
			"52" =>"",
			"53" =>"",
			"54" =>""
			);
		array_push($temp_sysResponse_1["news_returned"], $bio_array);
	}


if($input_show_shares == 1){

	$news_fetch_query =  "SELECT "  
	. NEWS_TABLE_NAME . ".shares4sale_id,  " 
	. NEWS_TABLE_NAME . ".news_id,  " 
	. SHARES4SALE_TABLE_NAME . ".selling_price,  " 
	. SHARES4SALE_TABLE_NAME . ".currency,  " 
	. SHARES4SALE_TABLE_NAME . ".num_on_sale,  " 
	. SHARES4SALE_TABLE_NAME . ".number_sold,  " 
	. SHARES4SALE_TABLE_NAME . ".parent_shares_id,  " 
	. NEWS_TABLE_NAME . ".inputtor_id,  " 
	. SHARES_HOSTED_TABLE_NAME . ".share_name,  " 
	. SHARES_HOSTED_TABLE_NAME . ".shares_logo,  " 
	. SHARES_HOSTED_TABLE_NAME . ".yield_per_share, "
	. SHARES_HOSTED_TABLE_NAME . ".yield_duration, "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares, "
	. USER_BIO_TABLE_NAME . ".profile_picture,  " 
	. USER_BIO_TABLE_NAME . ".pot_name FROM " 
	. SHARES4SALE_TABLE_NAME . " INNER JOIN " 
	. NEWS_TABLE_NAME . " ON  "  
	. SHARES4SALE_TABLE_NAME . ".sharesOnSale_id="  
	. NEWS_TABLE_NAME . ".shares4sale_id INNER JOIN "
	. USER_BIO_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. NEWS_TABLE_NAME . ".inputtor_id INNER JOIN "
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id="  
	. SHARES4SALE_TABLE_NAME . ".parent_shares_id INNER JOIN "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " ON  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id="  
	. SHARES4SALE_TABLE_NAME . ".sharesOnSale_id "
	. " WHERE " . NEWS_TABLE_NAME . ".inputtor_id = ?  AND " . SHARES4SALE_TABLE_NAME . ".flag = 0 GROUP BY " . NEWS_TABLE_NAME . ".shares4sale_id ORDER BY  " . NEWS_TABLE_NAME . ".sku DESC LIMIT 15";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($db_investor_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		NEWS_TABLE_NAME . ".shares4sale_id", 
		NEWS_TABLE_NAME . ".news_id", 
		SHARES4SALE_TABLE_NAME . ".selling_price", 
		SHARES4SALE_TABLE_NAME . ".currency", 
		SHARES4SALE_TABLE_NAME . ".num_on_sale", 
		SHARES4SALE_TABLE_NAME . ".number_sold", 
		SHARES4SALE_TABLE_NAME . ".parent_shares_id", 
		NEWS_TABLE_NAME . ".inputtor_id", 
		SHARES_HOSTED_TABLE_NAME . ".share_name", 
		SHARES_HOSTED_TABLE_NAME . ".shares_logo", 
		SHARES_HOSTED_TABLE_NAME . ".yield_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".yield_duration", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares", 
		USER_BIO_TABLE_NAME . ".profile_picture", 
		USER_BIO_TABLE_NAME . ".pot_name"
	), 15, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($shares4sale_id, $news_id, $selling_price, $item_currency, $num_on_sale, $number_sold, $parent_shares_id, $inputtor_id, $share_name, $shares_logo, $yield_per_share, $yield_duration, $num_of_shares, $profile_picture, $pot_name);


	while($prepared_statement_results_array->fetch()){
		if($num_of_shares < ($num_on_sale - $number_sold) || ($num_on_sale - $number_sold) < 1){
			continue;
		}

		if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
			$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
		} else {
			$profile_picture = "";
		}

		if(trim($shares_logo) != "" && $validatorObject->fileExists("../../user/" . $shares_logo) !== false){
			$shares_logo = HTTP_HEAD . "://fishpott.com/user/" . $shares_logo;
		} else {
			$shares_logo = "";
		}

		$selling_price = floatval($selling_price);
		$num_on_sale_final = intval($num_on_sale) - intval($number_sold);
		$item_currency = trim($item_currency);
		$yield_per_share = floatval($yield_per_share);
		$yield_duration = strval($yield_duration);

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$added_item_selling_price = $miscellaneousObject->convertPriceToNewCurrency($item_currency, $selling_price, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, true);
		
		$added_item_selling_price = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $added_item_selling_price;

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$yield_per_share = $miscellaneousObject->convertPriceToNewCurrency("USD", $yield_per_share * $num_on_sale_final, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$yield_per_share = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $yield_per_share;
		
		//FETCHING THE SHARES VALUE CHANGE / YIELD INFO STATS INFO
		//$added_item_long_note = $languagesObject->getLanguageString("buying_all_these_shares_for_sale_will_make_you", $input_language) . $yield_per_share . $languagesObject->getLanguageString("every", $input_language) . $yield_duration . $languagesObject->getLanguageString("days", $input_language);

		// FETCHING SHARES SELLING POINT INFO
		$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(DISTINCT owner_id) FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($parent_shares_id));
		if($prepared_statement_2 === false){
			continue;
		}
		$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
		if($prepared_statement_results_array_2 === false){
			continue;
		}
		$added_item_short_note = trim(strval($prepared_statement_results_array_2[0])) . $languagesObject->getLanguageString("potts_own_this_shares", $input_language);
		if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] <= 1){
			$added_item_short_note = $share_name;
		}

		//GETTING THE LATEST NEWS ID OF THE SHARES FOR SALE
		$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT news_id FROM " . NEWS_TABLE_NAME . " WHERE shares4sale_id = ? ORDER BY sku DESC", 1, "s", array($shares4sale_id));
		if($prepared_statement_2 === false){
			continue;
		}
		$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("news_id"), 1, 1);
		if($prepared_statement_results_array_2 === false){
			continue;
		}

		$latest_news_id = $prepared_statement_results_array_2[0];

			$next  = array(				
				"0a" => $profile_picture, //int pottpic;
				"1" => $shares_logo, //int shares_logo;
				"2" => $added_item_short_note, //String added_item_short_note;
				"3" => $added_item_selling_price, //String added_item_selling_price;
				"4" => $num_on_sale_final, //String num_on_sale_final;
				"5" => $latest_news_id, //String news_id;
				"6" => $parent_shares_id, //String ;
				"7" => $pot_name, //String ;
				"8" => $shares4sale_id, //String ;
				"9" => $share_name //String pot_name;
				);
			array_push($sysResponse["shares_returned"], $next);
	}
}

if($input_lastest_sku <= 0){
	$input_lastest_sku = 0;
	$sku_query_addition = " AND " . NEWS_TABLE_NAME . ".inputtor_id  = ? ";
	$value_type_string = "s";
	$value_array = array($db_investor_id);
} else {
	$sku_query_addition = " AND " . NEWS_TABLE_NAME . ".sku < ?  AND " . NEWS_TABLE_NAME . ".inputtor_id  = ? ";
	$value_type_string = "is";
	$value_array = array($input_lastest_sku, $db_investor_id);
}

// GETTING NEWSFEED
$news_fetch_query =  "SELECT " 
. NEWS_TABLE_NAME . ".sku,  " 
. NEWS_TABLE_NAME . ".type,  " 
. NEWS_TABLE_NAME . ".news_id,  " 
. NEWS_TABLE_NAME . ".news,  " 
. NEWS_TABLE_NAME . ".date_time,  " 
. NEWS_TABLE_NAME . ".inputtor_id,  " 
. NEWS_TABLE_NAME . ".added_item_news_id,  " 
. NEWS_TABLE_NAME . ".news_image, "  
. NEWS_TABLE_NAME . ".added_item_type, "   
. NEWS_TABLE_NAME . ".news_video, "  
. USER_BIO_TABLE_NAME . ".pot_name, "  
. USER_BIO_TABLE_NAME . ".first_name, "
. USER_BIO_TABLE_NAME . ".last_name, "
. USER_BIO_TABLE_NAME . ".verified_tag, "
. USER_BIO_TABLE_NAME . ".profile_picture, "  
. LOGIN_TABLE_NAME . ".login_type, "   
. NEWS_TABLE_NAME . ".news_id_ref, "  
. NEWS_TABLE_NAME . ".shares4sale_id FROM "  
. NEWS_TABLE_NAME . " INNER JOIN " 
. USER_BIO_TABLE_NAME . " ON  "  
. NEWS_TABLE_NAME . ".inputtor_id="  
. USER_BIO_TABLE_NAME . ".investor_id INNER JOIN "
. LOGIN_TABLE_NAME . " ON  "  
. LOGIN_TABLE_NAME . ".id="  
. USER_BIO_TABLE_NAME . ".investor_id "
. " WHERE " . NEWS_TABLE_NAME . ".flag = 0 $sku_query_addition ORDER BY  " . NEWS_TABLE_NAME . ".sku DESC LIMIT 30";


// GETTING THE NEWS CONTENT
$temp_sysResponse_1 = getNewsForNewsfeed($news_fetch_query, $value_type_string, $value_array, 30, $input_id, $input_mypottname, $newsObject, $languagesObject, $timeObject, $preparedStatementObject, $miscellaneousObject, $dbObject, $temp_sysResponse_1, $input_my_currency, $input_language, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject);

$sysResponse["news_returned"] = $temp_sysResponse_1["news_returned"];

	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "",  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on,
		'8' => $transfer_fee
		);

	 //var_dump($json_response);
	 //echo "here 999 \n";
	 echo safe_json_encode($sysResponse);

	// CLOSE DATABASE CONNECTION
	if($prepared_statement !== false){
		$dbObject->closeDatabaseConnection($prepared_statement);
	}
	exit;
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

function getNewsForNewsfeed($news_fetch_query, $value_type_string, $value_array, $maximum_rows, $fetcher_id, $fetcher_pottname, $newsObject, $languagesObject, $timeObject, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $input_my_currency, $input_language, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject) {

	//if(rand(1,5))

	// RANDOMLY CHOOSE NEWS PATTERN OF HOW HORIZONTAL AND VERTICAL NEWS MIX
	// IF FETCH TYPE IS 1, GET NEWS IN DESCENDING ORDER OF SKU AS MANY AS POSSIBLE THAT MEETS THE TIME CRITERIA. NEWS MUST START FROM THE LATEST SKU 
	// IF FETCH TYPE IS NOT 1, GE

	

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($value_array), $value_type_string, $value_array);

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
			NEWS_TABLE_NAME . ".sku", 
			NEWS_TABLE_NAME . ".type", 
			NEWS_TABLE_NAME . ".news_id", 
			NEWS_TABLE_NAME . ".news", 
			NEWS_TABLE_NAME . ".date_time", 
			NEWS_TABLE_NAME . ".inputtor_id", 
			NEWS_TABLE_NAME . ".added_item_news_id", 
			NEWS_TABLE_NAME . ".news_image", 
			NEWS_TABLE_NAME . ".added_item_type", 
			NEWS_TABLE_NAME . ".news_video", 
			USER_BIO_TABLE_NAME . ".pot_name", 
			USER_BIO_TABLE_NAME . ".first_name", 
			USER_BIO_TABLE_NAME . ".last_name", 
			USER_BIO_TABLE_NAME . ".verified_tag", 
			USER_BIO_TABLE_NAME . ".profile_picture",
			LOGIN_TABLE_NAME . ".login_type",
			NEWS_TABLE_NAME . ".news_id_ref",
			NEWS_TABLE_NAME . ".shares4sale_id"
		), 18, 2);

		//BINDING THE RESULTS TO VARIABLES
		$prepared_statement_results_array->bind_result($sku, $news_type, $news_id, $news_text, $date_time, $inputtor_id, $added_item_news_id, $news_image, $added_item_type, $news_video, $pot_name, $first_name, $last_name, $verified_tag, $profile_picture, $newsmaker_accounttype, $news_id_ref, $shares4sale_id);

		// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
	    while($prepared_statement_results_array->fetch()){
				//$sku = 0; //int newsSku;
				$news_type_real = 0; //int newsType;
				//$news_id = ""; //String newsId;
				//$news_text = ""; //String newsText;
				$newsTime = ""; //String newsTime;
				$news_likes = ""; //String newsLikes;
				$news_dislikes = ""; //String newsDislikes;
				$news_comments = ""; //String newsComments;
				$news_views = ""; //String newsViews;
				$news_buyers = ""; //String newsTransactions;
				//$inputtor_id = ""; //String newsMakerId;
				//$pot_name = ""; //String newsMakerPottName;
				$newsMakerFullName = ""; //String newsMakerFullName;
				//$profile_picture = ""; //String newsMakerPottPic;
				$fetcher_like_status = 0; //int newViewerReactionStatus;
				//$added_item_news_id = ""; //String newsAddedItemId;
				$added_item_selling_price = ""; //String newsAddedItemPrice;
				$added_item_item_logo = ""; //String newsAddedItemIcon;
				$added_item_num_on_sale = ""; //String newsAddedItemQuantity;
				//$newsmaker_accounttype = 0; //int newsMakerAccountType;
				//$verified_tag = 0; //int newsMakerAccountVerifiedStatus;
				//$news_image = ""; //String newsImagesLinksSeparatedBySpaces;
				$added_item_status = 0; //int newsAddedItemStatus;
				//$news_video = ""; //String newsVideosLinksSeparatedBySpaces;
				//$news_image = ""; //String newsVideosCoverArtsLinksSeparatedBySpaces;
				$news_link = ""; //String newsUrl;
				$news_link_title = ""; //String newsUrlTitle;
				$added_item_short_note = ""; //String newsItemName;
				$added_item_long_note = ""; //String newsItemLocation;
				$reposter_pottName = ""; //String reposterPottName;
				$reposted_news_text = ""; //String repostedText;
				$reposted_item_icon = ""; //String repostedIcon;
				$reposted_item_price = ""; //String repostedItemPrice;
				$newsBackgroundColor = 0; //int newsBackgroundColor;
				$reposted_item_id = ""; //String repostedItemId;
				$reposted_news_id = ""; //String repostedNewsId;
				$added_item_parent_shares_id = "";
				$added_item_name = "";
				$reposted_fetcher_like_status = -1;
				$reposted_item_id = "";
				$reposted_item_price = "";
				$reposted_item_icon = "";
				$repost_added_item_parent_shares_id = "";
				$repost_added_item_name = "";
				$repost_added_item_quantity = "";

	    		if($news_type == "event" || $news_type == "fundraiser" || $news_type == "up4sale"){
	    			continue;
	    		}
				if(strtolower($newsmaker_accounttype) == "business"){
					$newsmaker_accounttype = 2;
				} else {
					$newsmaker_accounttype = 1;
				}
				if(trim($news_image) != "" && $validatorObject->fileExists("../../user/" . $news_image) !== false){
					$news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image;
				} else {
					$news_image = "";
				}				
				// GETTING REPOSTED NEWS INFO
				if($news_type == "shared_news"){

						$reposter_pottName = $pot_name;
						$reposted_news_text = $news_text;
						$reposted_news_id = $news_id;	

						if($added_item_news_id != ""){
							//echo "here 1 \n";
							$repost_added_item_info_array = getAddedItemToNewsOrRepost($dbObject, $preparedStatementObject, $miscellaneousObject, $languagesObject, $validatorObject, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $added_item_news_id, $input_my_currency);
							//var_dump($repost_added_item_info_array);

							$reposted_item_id = $repost_added_item_info_array[1];
							$reposted_item_price = $repost_added_item_info_array[7];
							$reposted_item_icon = $repost_added_item_info_array[10];
							$repost_added_item_parent_shares_id = $repost_added_item_info_array[11];
							$repost_added_item_name = $repost_added_item_info_array[2];
							$repost_added_item_quantity = $repost_added_item_info_array[8];
						} else {
							//echo "here 2 \n";
							$reposted_item_id = "";
							$reposted_item_price = "";
							$reposted_item_icon = "";
							$repost_added_item_parent_shares_id = "";
							$repost_added_item_name = "";
							$repost_added_item_quantity = "";
						}

						// CHECKING FETCHER LIKE STATUS // 1 = LIKE, 0 = DISLIKE, -1 = NOTHING
						$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT like_type, sku FROM " . LIKES_TABLE_NAME . " WHERE likes_news_id = ? AND liker_investor_id = ?", 2, "ss", array($reposted_news_id, $fetcher_id));
						if($prepared_statement_2 === false){
							continue;
						}
						$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("like_type", "sku"), 2, 1);
						if($prepared_statement_results_array_2 === false){
							continue;
						}
						$reposted_fetcher_like_status = $prepared_statement_results_array_2[0];
						if($prepared_statement_results_array_2[1] == 0){
							$reposted_fetcher_like_status = -1;
						}
						  $news_fetch_query2 =  "SELECT " 
						  . NEWS_TABLE_NAME . ".sku,  " 
						  . NEWS_TABLE_NAME . ".type,  " 
						  . NEWS_TABLE_NAME . ".news_id,  " 
						  . NEWS_TABLE_NAME . ".news,  " 
						  . NEWS_TABLE_NAME . ".date_time,  " 
						  . NEWS_TABLE_NAME . ".inputtor_id,  " 
						  . NEWS_TABLE_NAME . ".added_item_news_id,  " 
						  . NEWS_TABLE_NAME . ".news_image, "  
						  . NEWS_TABLE_NAME . ".added_item_type, "   
						  . NEWS_TABLE_NAME . ".news_video, "  
						  . USER_BIO_TABLE_NAME . ".pot_name, "  
						  . USER_BIO_TABLE_NAME . ".first_name, "
						  . USER_BIO_TABLE_NAME . ".last_name, "
						  . USER_BIO_TABLE_NAME . ".verified_tag, "
						  . USER_BIO_TABLE_NAME . ".profile_picture, "  
						  . LOGIN_TABLE_NAME . ".login_type FROM " 
						  . NEWS_TABLE_NAME . " INNER JOIN " 
						  . USER_BIO_TABLE_NAME . " ON  "  
						  . NEWS_TABLE_NAME . ".inputtor_id="  
						  . USER_BIO_TABLE_NAME . ".investor_id INNER JOIN "
						  . LOGIN_TABLE_NAME . " ON  "  
						  . LOGIN_TABLE_NAME . ".id="  
						  . USER_BIO_TABLE_NAME . ".investor_id "
						  . " WHERE " . NEWS_TABLE_NAME . ".news_id = ? AND " . NEWS_TABLE_NAME . ".flag = 0";

						// GETTING NEWS LINK DETECTED
						$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query2, 1, "s", array($news_id_ref));

						if($prepared_statement_2 === false){
							continue;
						}

						$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array(
							NEWS_TABLE_NAME . ".sku", 
							NEWS_TABLE_NAME . ".type", 
							NEWS_TABLE_NAME . ".news_id", 
							NEWS_TABLE_NAME . ".news", 
							NEWS_TABLE_NAME . ".date_time", 
							NEWS_TABLE_NAME . ".inputtor_id", 
							NEWS_TABLE_NAME . ".added_item_news_id", 
							NEWS_TABLE_NAME . ".news_image", 
							NEWS_TABLE_NAME . ".added_item_type", 
							NEWS_TABLE_NAME . ".news_video", 
							USER_BIO_TABLE_NAME . ".pot_name", 
							USER_BIO_TABLE_NAME . ".first_name", 
							USER_BIO_TABLE_NAME . ".last_name", 
							USER_BIO_TABLE_NAME . ".verified_tag", 
							USER_BIO_TABLE_NAME . ".profile_picture",
							LOGIN_TABLE_NAME . ".login_type"
						), 16, 1);

						$sku = $prepared_statement_results_array_2[0];
						$news_type = $prepared_statement_results_array_2[1];
						$news_id = $prepared_statement_results_array_2[2];
						$news_text = $prepared_statement_results_array_2[3];
						$date_time = $prepared_statement_results_array_2[4];
						$inputtor_id = $prepared_statement_results_array_2[5];
						$added_item_news_id = $prepared_statement_results_array_2[6];
						$news_image = $prepared_statement_results_array_2[7];
						if(trim($news_image) != "" && $validatorObject->fileExists("../../user/" . $news_image) !== false){
							$news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image;
						} else {
							$news_image = "";
						}						
						$added_item_type = $prepared_statement_results_array_2[8];
						$news_video = $prepared_statement_results_array_2[9];
						$pot_name = $prepared_statement_results_array_2[10];
						$first_name = $prepared_statement_results_array_2[11];
						$last_name = $prepared_statement_results_array_2[12];
						$verified_tag = $prepared_statement_results_array_2[13];
						$profile_picture = $prepared_statement_results_array_2[14];
						$newsmaker_accounttype = $prepared_statement_results_array_2[15];
						if(strtolower($newsmaker_accounttype) == "business"){
							$newsmaker_accounttype = 2;
						} else {
							$newsmaker_accounttype = 1;
						}
						//$news_text = $prepared_statement_results_array_2[16];

					} else {
						$reposter_pottName = "";
					}


	    		//GETTING ALL NEWS IMAGES
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT link_address  FROM " . NEWS_IMAGES_TABLE_NAME . " WHERE news_id = ? AND flag = 0", 1, "s", array($news_id));

				if($prepared_statement_2 === false){
					continue;
				}

				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("link_address"), 1, 2);

				$prepared_statement_results_array_2->bind_result($link_address);

			    while($prepared_statement_results_array_2->fetch()){
					if(trim($link_address) != "" && $validatorObject->fileExists("../../user/" . $link_address) !== false){
						$link_address = HTTP_HEAD . "://fishpott.com/user/" . $link_address;
				    	$news_image .= " ";
				    	$news_image .= $link_address;
					} else {
						continue;
					}
			    }

			    // GETTING NEWS LINK DETECTED
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT link, link_title, cover_image, video_detected FROM " . NEWS_LINKS_TABLE_NAME . " WHERE news_id = ? AND flag = 0", 1, "s", array($news_id));

				if($prepared_statement_2 === false){
					continue;
				}

				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("link", "link_title", "cover_image", "video_detected"), 4, 1);

				if($prepared_statement_results_array_2 === false){
					continue;
				}

				$news_link = trim($prepared_statement_results_array_2[0]);
				$news_link_title = trim($prepared_statement_results_array_2[1]);
				$news_link_cover_image = trim($prepared_statement_results_array_2[2]);
				$news_link_video_detected = trim($prepared_statement_results_array_2[3]);

				if($prepared_statement_results_array_2[0] == "link"){
					$news_link = "";
					$news_link_title = "";
					$news_link_cover_image = "";
					$news_link_video_detected = "";
				}

			    // CHECKING FETCHER LIKE STATUS // 1 = LIKE, 0 = DISLIKE, -1 = NOTHING
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT like_type, sku FROM " . LIKES_TABLE_NAME . " WHERE likes_news_id = ? AND liker_investor_id = ?", 2, "ss", array($news_id, $fetcher_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("like_type", "sku"), 2, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$fetcher_like_status = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[1] == 0){
					$fetcher_like_status = -1;
				}
				
			    // GETTING LIKES
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . LIKES_TABLE_NAME . " WHERE likes_news_id = ? AND like_type = 1", 1, "s", array($news_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$news_likes = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] == 0){
					$news_likes = "";
				} else {
					$news_likes = trim(strval($news_likes));
				}

				
			    // GETTING DISLIKES
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . LIKES_TABLE_NAME . " WHERE likes_news_id = ? AND like_type = 0", 1, "s", array($news_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$news_dislikes = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] == 0){
					$news_dislikes = "";
				} else {
					$news_dislikes = trim(strval($news_dislikes));
				}
				
			    // GETTING COMMENTS
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . COMMENTS_TABLE_NAME . " WHERE news_id = ? AND flag = 0", 1, "s", array($news_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$news_comments = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] == 0){
					$news_comments = "";
				} else {
					$news_comments = trim(strval($news_comments));
				}
				
			    // GETTING PURCHASES
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . PURCHASES_TABLE_NAME . " WHERE adetor_news_id = ? AND flag = 0", 1, "s", array($news_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$news_buyers = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] == 0){
					$news_buyers = "";
				} else {
					$news_buyers = trim(strval($news_buyers));
				}
				
			    // GETTING VIEWS
			    $view_id = $news_id . "_" . $fetcher_id;
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(*) FROM " . VIEWS_TABLE_NAME . " WHERE news_id = ? ", 1, "s", array($news_id));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
				if($prepared_statement_results_array_2 === false){
					continue;
				}
				$news_views = $prepared_statement_results_array_2[0];
				if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] == 0){
					$news_views = "";
				} else {
					$news_views = trim(strval($news_views));
				}


				// FORMATING NEWS REACTION STRINGS
				if($news_likes != "" && ($news_dislikes != "" || $news_comments != "" || $news_views != "" || $news_buyers != "")){
					$news_likes = $news_likes  . " " . $languagesObject->getLanguageString("likes", $input_language) . " - ";
				} else if($news_likes != "") {
					$news_likes = $news_likes  . " " . $languagesObject->getLanguageString("likes", $input_language);
				}

				if($news_dislikes != "" && ($news_comments != "" || $news_views != "" || $news_buyers != "")){
					$news_dislikes = $news_dislikes . " " . $languagesObject->getLanguageString("dislikes", $input_language) .  " - ";
				} else if($news_dislikes != ""){
					$news_dislikes = $news_dislikes . " " . $languagesObject->getLanguageString("dislikes", $input_language);
				}

				if($news_comments != "" && ($news_views != "" || $news_buyers != "")){
					$news_comments = $news_comments . " " . $languagesObject->getLanguageString("comments", $input_language) . " - ";
				} else if($news_comments != ""){
					$news_comments = $news_comments . " " . $languagesObject->getLanguageString("comments", $input_language);
				}

				if($news_views != ""){
					$news_views = $news_views . " " . $languagesObject->getLanguageString("views", $input_language);
				}

				if($news_buyers != ""){
					$news_buyers = $news_buyers . " " . $languagesObject->getLanguageString("buyers", $input_language);
				}

				
				// CHECKING THAT PROFILE PICTURE EXISTS
				if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
					$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
				} else {
					$profile_picture = "";
				}


				// CHECKING THAT VIDEO EXISTS
				if(trim($news_video) != "" && $validatorObject->fileExists("../../user/" . $news_video) !== false){
					$news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video;
				} else {
					$news_video = "";
				}

				//GET ADDED ITEM INFO
				if($added_item_news_id != "" || ($news_type == "shares4sale" && $shares4sale_id != "")){
					if($news_type == "shares4sale"){
						$added_item_news_id = $shares4sale_id;
					}
					$news_added_item_info_array = getAddedItemToNewsOrRepost($dbObject, $preparedStatementObject, $miscellaneousObject, $languagesObject, $validatorObject, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $added_item_news_id, $input_my_currency);

					//var_dump($news_added_item_info_array);

					$added_item_status = $news_added_item_info_array[0];
					$added_item_news_id = $news_added_item_info_array[1];
					$added_item_name = $news_added_item_info_array[2] ;
					$added_item_yield_per_share = $news_added_item_info_array[3];
					$added_item_yield_duration = $news_added_item_info_array[4];
					$added_item_short_note = $news_added_item_info_array[5];
					$added_item_long_note = $news_added_item_info_array[6];
					$added_item_selling_price = $news_added_item_info_array[7];
					$added_item_num_on_sale = $news_added_item_info_array[8];
					$added_item_currency = $news_added_item_info_array[9];
					$added_item_item_logo = $news_added_item_info_array[10];
					$added_item_parent_shares_id = $news_added_item_info_array[11];
					$added_item_distinct_owners = $news_added_item_info_array[12];

					if($news_type == "shares4sale" && $added_item_news_id == ""){
						continue;
					}

				} else {
					$added_item_news_id = "";
					$added_item_status = 0;
				}

				$news_type_real = $newsObject->getNewsType($news_type, $news_text, $news_video, $news_image, $news_link, $news_link_cover_image, $news_link_video_detected, true, "", "", "", "", $reposter_pottName);

				if($news_type_real == ""){
					continue;
				} else if($news_type_real == 10 || $news_type_real == 11 || $news_type_real == 25 || $news_type_real == 26){
					//NEWS_TYPE_7_AND_9_JUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY 
					//OR 
					//NEWS_TYPE_8_JUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY 
					// OR THEIR REPOSTED TYPES
					$news_image = $news_link_cover_image;
					$news_video = $news_link_video_detected;
				}

				

				if($added_item_status == 1){
					if($news_type_real == NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY){

						$news_type_real = NEWS_TYPE_41_SHARESFORSALE_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY;

					} else if($news_type_real == NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY){

						$news_type_real = NEWS_TYPE_42_SHARESFORSALE_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY;

					} else if($news_type_real == NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY){

						$news_type_real = NEWS_TYPE_43_SHARESFORSALE_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY;

					} else if($news_type_real == NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY){

						$news_type_real = NEWS_TYPE_44_SHARESFORSALE_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY;
						
					}
				}

				if($news_text == null){
					$news_text = "";
				}

	    		// SETTING THE DEFAULT VALUES FOR THE RETURN ARRAY ELEMENTS VALUES
				$newsTime = $timeObject->getTimeElapsedSstring($date_time, false);
				$newsMakerFullName = $first_name . " " . $last_name;
				$newsBackgroundColor = $newsObject->getNewsBackGroundColor($news_type_real);

				$news_video = str_replace(" ", "%20", $news_video);
				//$news_image = str_replace(" ", "%20", $news_image);
				//$profile_picture = str_replace(" ", "%20", $profile_picture);
				//$added_item_item_logo = str_replace(" ", "%20", $added_item_item_logo);

			$next  = array(				
				"0a" => $sku, //int newsSku;
				"1" => $news_type_real, //int newsType;
				"2" => $news_id, //String newsId;
				"3" => $news_text, //String newsText;
				"4" => $newsTime, //String newsTime;
				"5" => $news_likes, //String newsLikes;
				"6" => $news_dislikes, //String newsDislikes;
				"7" => $news_comments, //String newsComments;
				"8" => $news_views, //String newsViews;
				"9" => "", //String newsReposts;
				"10" => $news_buyers, //String newsTransactions;
				"11" => $inputtor_id, //String newsMakerId;
				"12" => $pot_name, //String newsMakerPottName;
				"13" => $newsMakerFullName, //String newsMakerFullName;
				"14" => $profile_picture, //String newsMakerPottPic;
				"15" => $fetcher_like_status, //int newViewerReactionStatus;
				"16" => $added_item_news_id, //String newsAddedItemId;
				"17" => $added_item_selling_price, //String newsAddedItemPrice;
				"18" => $added_item_item_logo, //String newsAddedItemIcon;
				"19" => $added_item_num_on_sale, //String newsAddedItemQuantity;
				"20" => $newsmaker_accounttype, //int newsMakerAccountType;
				"21" => $verified_tag, //int newsMakerAccountVerifiedStatus;
				"22" => $news_image, //String newsImagesLinksSeparatedBySpaces;
				"23" => "0", //String newsImagesCount;
				"24" => 0, //int newsTextReadMoreToggle;
				"25" => 1, //int newsAddedItemType;
				"26" => $added_item_status, //int newsAddedItemStatus;
				"27" => $news_video, //String newsVideosLinksSeparatedBySpaces;
				"28" => $news_image, //String newsVideosCoverArtsLinksSeparatedBySpaces;
				"29" => "1", //String newsVideosCount;
				"30" => $news_link, //String newsUrl;
				"31" => $news_link_title, //String newsUrlTitle;
				"32" => $added_item_short_note, //String newsItemName;
				"33" => $added_item_long_note, //String newsItemLocation;
				"34" => 0, //int newsItemVerifiedStatus;
				"35" => "", //String advertItemIcon;
				"36" => "", //String advertTextTitle;
				"37" => "", //String advertTextTitle2;
				"38" => "", //String advertButtonText;
				"39" => "", //String advertLink;
				"40" => $reposter_pottName, //String reposterPottName;
				"41" => $reposted_news_text, //String repostedText;
				"42" => $reposted_item_icon, //String repostedIcon;
				"43" => $reposted_fetcher_like_status, //int newRepostedViewerReactionStatus; -- SHOWS THAT VIEWS IS BEING SHOWN
				"44" => $reposted_item_price, //String repostedItemPrice;
				"45" => $newsBackgroundColor, //int newsBackgroundColor;
				"46" => 0, //int newsViewsRepostOrPurchasesShowStatus;
				"47" => $reposted_item_id, //String repostedItemId;
				"48" => $reposted_news_id, //String repostedNewsId;
				"49" => $added_item_parent_shares_id, //String repostedNewsId;
				"50" => $added_item_name, //String repostedNewsId;
				"51" => $added_item_num_on_sale, //String repostedNewsId;
				"52" => $repost_added_item_parent_shares_id, //String repostedNewsId;
				"53" => $repost_added_item_name, //String repostedNewsId;
				"54" => $repost_added_item_quantity

				);
			array_push($sysResponse["news_returned"], $next);


	}
	return $sysResponse;

} // END

function getAddedItemToNewsOrRepost($dbObject, $preparedStatementObject, $miscellaneousObject, $languagesObject, $validatorObject, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $added_item_news_id, $input_my_currency){
	$news_added_item_info_array = array(); 
	$query = "SELECT "
		. SHARES4SALE_TABLE_NAME . ".number_sold,  " 
		. SHARES4SALE_TABLE_NAME . ".selling_price,  " 
		. SHARES4SALE_TABLE_NAME . ".num_on_sale,  " 
		. SHARES4SALE_TABLE_NAME . ".currency,  " 
		. SHARES_HOSTED_TABLE_NAME . ".share_name,  " 
		. SHARES_HOSTED_TABLE_NAME . ".shares_logo,  " 
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id, "
		. SHARES_HOSTED_TABLE_NAME . ".yield_per_share, "
		. SHARES_HOSTED_TABLE_NAME . ".yield_duration, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares FROM "
        . SHARES4SALE_TABLE_NAME . " INNER JOIN " 
        . SHARES_HOSTED_TABLE_NAME . " ON  "  
        . SHARES4SALE_TABLE_NAME . ".parent_shares_id="  
        . SHARES_HOSTED_TABLE_NAME . ".parent_shares_id  INNER JOIN "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " ON  "  
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id="  
		. SHARES4SALE_TABLE_NAME . ".sharesOnSale_id "
        . " WHERE " . SHARES4SALE_TABLE_NAME . ".sharesOnSale_id = ? AND " . SHARES4SALE_TABLE_NAME . ".flag = 0";

	$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query , 1, "s", array($added_item_news_id));

	if($prepared_statement_2 === false){
		continue;
	}

	$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array(
		SHARES4SALE_TABLE_NAME . ".number_sold",
		SHARES4SALE_TABLE_NAME . ".selling_price",
		SHARES4SALE_TABLE_NAME . ".num_on_sale", 
		SHARES4SALE_TABLE_NAME . ".currency", 
		SHARES_HOSTED_TABLE_NAME . ".share_name",
		SHARES_HOSTED_TABLE_NAME . ".shares_logo",
		SHARES_HOSTED_TABLE_NAME . ".parent_shares_id",
		SHARES_HOSTED_TABLE_NAME . ".yield_per_share",
		SHARES_HOSTED_TABLE_NAME . ".yield_duration",
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares"
	), 10, 1);

	if($prepared_statement_results_array_2 === false){
		continue;
	}


	$selling_price = floatval($prepared_statement_results_array_2[1]);
	$added_item_num_on_sale = intval($prepared_statement_results_array_2[2]);
	//$added_item_num_on_sale = intval($prepared_statement_results_array_2[2]) - intval($prepared_statement_results_array_2[0]);
	$added_item_currency = trim($prepared_statement_results_array_2[3]);
	$added_item_name = trim($prepared_statement_results_array_2[4]);
	$added_item_item_logo = trim($prepared_statement_results_array_2[5]);
	$added_item_parent_shares_id = trim($prepared_statement_results_array_2[6]);
	$added_item_yield_per_share = floatval($prepared_statement_results_array_2[7]);
	$added_item_yield_duration = strval($prepared_statement_results_array_2[8]);
	$shares_owned = strval($prepared_statement_results_array_2[9]);

	// CONVERTING THE CURRENCY TO USER'S CURRENCY
	$added_item_selling_price = $miscellaneousObject->convertPriceToNewCurrency($added_item_currency, $selling_price, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, true);
	
	$added_item_selling_price = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $added_item_selling_price;

	// CONVERTING THE CURRENCY TO USER'S CURRENCY
	$added_item_yield_per_share = $miscellaneousObject->convertPriceToNewCurrency("USD", $added_item_yield_per_share * $added_item_num_on_sale, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

	$added_item_yield_per_share = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $added_item_yield_per_share;
	
	//FETCHING THE SHARES VALUE CHANGE / YIELD INFO STATS INFO
	$added_item_long_note = $languagesObject->getLanguageString("buying_all_these_shares_for_sale_will_make_you", $input_language) . $added_item_yield_per_share . $languagesObject->getLanguageString("every", $input_language) . $added_item_yield_duration . $languagesObject->getLanguageString("days", $input_language);

	// FETCHING SHARES SELLING POINT INFO
	$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT COUNT(DISTINCT owner_id) FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($added_item_parent_shares_id));
	if($prepared_statement_2 === false){
		continue;
	}
	$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("COUNT(*)"), 1, 1);
	if($prepared_statement_results_array_2 === false){
		continue;
	}
	$added_item_short_note = trim(strval($prepared_statement_results_array_2[0])) . $languagesObject->getLanguageString("potts_own_this_shares", $input_language);
	if($prepared_statement_results_array_2[0] == "COUNT(*)" || $prepared_statement_results_array_2[0] <= 1){
		$added_item_short_note = $added_item_name;
	}


	if(
		trim($added_item_item_logo) != "" 
		&& $validatorObject->fileExists("../../user/" . $added_item_item_logo) !== false 
		&& $added_item_num_on_sale > 0
		&& $selling_price > 0
		&& $shares_owned >= $added_item_num_on_sale
	){
		$added_item_item_logo = HTTP_HEAD . "://fishpott.com/user/" . $added_item_item_logo;
		$added_item_status = 1;
		$news_added_item_info_array[0] = $added_item_status;
		$news_added_item_info_array[1] = $added_item_news_id;
		$news_added_item_info_array[2] = $added_item_name;
		$news_added_item_info_array[3] = $added_item_yield_per_share;
		$news_added_item_info_array[4] = $added_item_yield_duration;
		$news_added_item_info_array[5] = $added_item_short_note;
		$news_added_item_info_array[6] = $added_item_long_note;
		$news_added_item_info_array[7] = $added_item_selling_price;
		$news_added_item_info_array[8] = $added_item_num_on_sale;
		$news_added_item_info_array[9] = $added_item_currency;
		$news_added_item_info_array[10] = $added_item_item_logo;
		$news_added_item_info_array[11] = $added_item_parent_shares_id;
		$news_added_item_info_array[12] = $prepared_statement_results_array_2[0];
	} else {
		$added_item_news_id = "";
		$added_item_status = 0;
		$news_added_item_info_array[0] = $added_item_status;
		$news_added_item_info_array[1] = $added_item_news_id;
	}

	return $news_added_item_info_array;

}
