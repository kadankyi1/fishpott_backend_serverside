<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["sales_lastsku"]) && trim($_POST["sales_lastsku"]) != "" && intval($_POST["sales_lastsku"]) > -1 &&
	isset($_POST["credit_lastsku"]) && trim($_POST["credit_lastsku"]) != "" && intval($_POST["credit_lastsku"]) > -1 &&
	isset($_POST["withdr_lastsku"]) && trim($_POST["withdr_lastsku"]) != "" && intval($_POST["withdr_lastsku"]) > -1 &&
	isset($_POST["cre_coup_lastsku"]) && trim($_POST["cre_coup_lastsku"]) != "" && intval($_POST["cre_coup_lastsku"]) > -1 &&
	isset($_POST["share_coup_lastsku"]) && trim($_POST["share_coup_lastsku"]) != "" && intval($_POST["share_coup_lastsku"]) > -1 &&
	isset($_POST["poach_last_sku"]) && trim($_POST["poach_last_sku"]) != "" && intval($_POST["poach_last_sku"]) > -1 &&
	isset($_POST["transfer_last_sku"]) && trim($_POST["transfer_last_sku"]) != "" && intval($_POST["transfer_last_sku"]) > -1 &&
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
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);
	

	$input_sales_lastsku = intval($_POST["sales_lastsku"]);
	$input_credit_request_lastsku = intval($_POST["credit_lastsku"]);
	$input_withdrawal_request_lastsku = intval($_POST["withdr_lastsku"]);
	$input_credit_coupon_request_lastsku = intval($_POST["cre_coup_lastsku"]);
	$input_shares_coupon_request_lastsku = intval($_POST["share_coup_lastsku"]);
	$input_poach_lastsku = intval($_POST["poach_last_sku"]);	
	$input_transfer_last_sku = intval($_POST["transfer_last_sku"]);	

	$sys_sales_lastsku = 0;
	$sys_credit_request_lastsku = 0;
	$sys_withdrawal_request_lastsku = 0;
	$sys_transfer_last_sku = 0;
	/*
	$sys_credit_coupon_request_lastsku = 0;
	$sys_shares_coupon_request_lastsku = 0;
	$sys_poach_lastsku = 0;	
	*/


	//DECLARING THE ARRAY FOR THE RESULTS
	$sysResponse["news_returned"] = array();

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

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

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

	//UPDATING THE LAST SEEN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

/***********************************************************************************************************

							SALES / ADETOR START

***********************************************************************************************************/


	if($input_sales_lastsku == 0){
		$sys_sales_sku_query_addition = "";
		$sys_sales_query_values_array = array($input_id, $input_id);
		$sys_sales_query_values_types_string = "ss";
	} else {
		$sys_sales_sku_query_addition = " AND " . PURCHASES_TABLE_NAME . ".sku < ? ";
		$sys_sales_query_values_array = array($input_id, $input_id, $input_sales_lastsku);
		$sys_sales_query_values_types_string = "ssi";
	}

	$news_fetch_query =  "SELECT " 
	. PURCHASES_TABLE_NAME . ".sku,  " 
	. PURCHASES_TABLE_NAME . ".date_time,  " 
	. PURCHASES_TABLE_NAME . ".item_quantity,  " 
	. PURCHASES_TABLE_NAME . ".sale_real_amt_credited_to_seller_acc,  " 
	. PURCHASES_TABLE_NAME . ".total_charge_num,  "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id,  " 
	. SHARES_HOSTED_TABLE_NAME . ".share_name,  " 
	. SHARES_TRANSFER_TABLE_NAME . ".admin_review_status,  " 
	. USER_BIO_TABLE_NAME . ".currency,  " 
	. USER_BIO_TABLE_NAME . ".pot_name,  " 
	. PURCHASES_TABLE_NAME . ".buyer_id,  " 
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id FROM "  
	. PURCHASES_TABLE_NAME . " INNER JOIN " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " ON  "  
	. PURCHASES_TABLE_NAME . ".adetor_item_id="  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id INNER JOIN "
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id="  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id INNER JOIN "
	. SHARES_TRANSFER_TABLE_NAME . " ON  "  
	. SHARES_TRANSFER_TABLE_NAME . ".adetor_id="  
	. PURCHASES_TABLE_NAME . ".adetor_id_short  INNER JOIN "
	. USER_BIO_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. PURCHASES_TABLE_NAME . ".seller_id "
	. " WHERE ( " . PURCHASES_TABLE_NAME . ".buyer_id = ? OR " . PURCHASES_TABLE_NAME . ".seller_id = ? ) $sys_sales_sku_query_addition ORDER BY  " . PURCHASES_TABLE_NAME . ".sku DESC LIMIT 10";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($sys_sales_query_values_array), $sys_sales_query_values_types_string, $sys_sales_query_values_array);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		PURCHASES_TABLE_NAME . ".sku", 
		PURCHASES_TABLE_NAME . ".date_time", 
		PURCHASES_TABLE_NAME . ".item_quantity", 
		PURCHASES_TABLE_NAME . ".sale_real_amt_credited_to_seller_acc", 
		PURCHASES_TABLE_NAME . ".total_charge_num", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id", 
		SHARES_HOSTED_TABLE_NAME . ".share_name", 
		SHARES_TRANSFER_TABLE_NAME . ".admin_review_status", 
		USER_BIO_TABLE_NAME . ".currency", 
		USER_BIO_TABLE_NAME . ".pot_name", 
		PURCHASES_TABLE_NAME . ".buyer_id", 
		SHARES_HOSTED_TABLE_NAME . ".parent_shares_id"
	), 12, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($sku, $date_time, $item_quantity, $sale_real_amt_credited_to_seller_acc, $total_charge_num, $share_id, $share_name, $admin_review_status, $currency, $pot_name, $buyer_id, $parent_shares_id);

	while($prepared_statement_results_array->fetch()){

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$total_charge_in_users_currency = $input_my_currency . " " . $miscellaneousObject->convertPriceToNewCurrency("USD", $total_charge_num, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$sale_real_amt_credited_to_seller_acc_in_sellers_currency = $input_my_currency . " " . $miscellaneousObject->convertPriceToNewCurrency("USD", $sale_real_amt_credited_to_seller_acc, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
        
        $sys_status_num = intval($admin_review_status);

        if(intval($admin_review_status) == 1){
        	$sys_status = $languagesObject->getLanguageString("completed", $input_language);
        } else if (intval($admin_review_status) == 0){
        	$sys_status = $languagesObject->getLanguageString("pending", $input_language);
        } else if (intval($admin_review_status) == 2){
        	$sys_status = $languagesObject->getLanguageString("rejected", $input_language);
        } else {
        	$sys_status = $languagesObject->getLanguageString("error_2", $input_language);
        	$sys_status_num = 3;
        }

		if($buyer_id == $input_id){
			$next  = array(				
				"0a" => "SHARES PURCHASE", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
				"1" => $timeObject->reformatDate("j M, Y - g:i A", $date_time), //mine1.setDate(k.getString("1")); Eg: 2 Sep
				"2" => strval($item_quantity), //mine1.setQuantityOrAmount(k.getString("2"));
				"3" => $share_name, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
				"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
				"5" => $total_charge_in_users_currency, //mine1.setTotalCharge(k.getString("5"));
				"6" => $date_time,
				"7" => $pot_name,
				"8" => $parent_shares_id,
				"9" => $sys_status_num
			);
		} else {
		$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT pot_name FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($buyer_id));

		if($prepared_statement2 === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement2, array("pot_name"), 1, 1);

		if($prepared_statement_results_array2 !== false && $prepared_statement_results_array2[0] != "pot_name" && $prepared_statement_results_array2[0] != USER_BIO_TABLE_NAME . "pot_name"){
			$pot_name = $prepared_statement_results_array2[0];
		} else {
			$pot_name = "";
		}


			$next  = array(				
				"0a" => "SHARES SALE", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
				"1" => $timeObject->reformatDate("j M, Y - g:i A", $date_time), //mine1.setDate(k.getString("1")); Eg: 2 Sep
				"2" => strval($item_quantity), //mine1.setQuantityOrAmount(k.getString("2"));
				"3" => $share_name, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
				"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
				"5" => $sale_real_amt_credited_to_seller_acc_in_sellers_currency, //mine1.setTotalCharge(k.getString("5"));
				"6" => $date_time,
				"7" => $pot_name,
				"8" => $parent_shares_id,
				"9" => $sys_status_num
			);
		}

		$sys_sales_lastsku = $sku;
		array_push($sysResponse["news_returned"], $next);
	}

/***********************************************************************************************************

							SALES / ADETOR END

***********************************************************************************************************/
/***********************************************************************************************************

							CREDIT REQUESTS START

***********************************************************************************************************/
	if($input_credit_request_lastsku == 0){
		$sys_credit_request_sku_query_addition = "";
		$sys_credit_request_query_values_array = array($input_id);
		$sys_credit_request_query_values_types_string = "s";
	} else {
		$sys_credit_request_sku_query_addition = " AND " . MONEY_CREDIT_TABLE_NAME . ".sku < ? ";
		$sys_credit_request_query_values_array = array($input_id, $input_credit_request_lastsku);
		$sys_credit_request_query_values_types_string = "si";
	}

	$news_fetch_query =  "SELECT " 
	. MONEY_CREDIT_TABLE_NAME . ".sku,  " 
	. MONEY_CREDIT_TABLE_NAME . ".input_date,  " 
	. MONEY_CREDIT_TABLE_NAME . ".amount_sent,  " 
	. MONEY_CREDIT_TABLE_NAME . ".pay_type,  " 
	. MONEY_CREDIT_TABLE_NAME . ".done_status,  "
	. MONEY_CREDIT_TABLE_NAME . ".transaction_id,  " 
	. MONEY_CREDIT_TABLE_NAME . ".currency_sent,  " 
	. MONEY_CREDIT_TABLE_NAME . ".investor_id FROM "
	. MONEY_CREDIT_TABLE_NAME . " "
	. " WHERE " . MONEY_CREDIT_TABLE_NAME . ".investor_id = ? $sys_credit_request_sku_query_addition ORDER BY  " . MONEY_CREDIT_TABLE_NAME . ".sku DESC LIMIT 10";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($sys_credit_request_query_values_array), $sys_credit_request_query_values_types_string, $sys_credit_request_query_values_array);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		MONEY_CREDIT_TABLE_NAME . ".sku", 
		MONEY_CREDIT_TABLE_NAME . ".input_date", 
		MONEY_CREDIT_TABLE_NAME . ".amount_sent", 
		MONEY_CREDIT_TABLE_NAME . ".pay_type", 
		MONEY_CREDIT_TABLE_NAME . ".done_status", 
		MONEY_CREDIT_TABLE_NAME . ".transaction_id", 
		MONEY_CREDIT_TABLE_NAME . ".currency_sent", 
		MONEY_CREDIT_TABLE_NAME . ".investor_id"
	), 8, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($sku, $input_date, $amount_sent, $pay_type, $done_status, $transaction_id, $currency_sent, $investor_id);

	while($prepared_statement_results_array->fetch()){

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$total_amount_in_users_currency = $input_my_currency . " " . $miscellaneousObject->convertPriceToNewCurrency($currency_sent, $amount_sent, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

        if(trim($done_status) == "completed"){
        	$sys_status = $languagesObject->getLanguageString("completed", $input_language);
        	$sys_status_num = 1;
        } else if (trim($done_status) == "pending"){
        	$sys_status = $languagesObject->getLanguageString("pending", $input_language);
        	$sys_status_num = 0;
        } else if (trim($done_status) == "cancelled"){
        	$sys_status = $languagesObject->getLanguageString("rejected", $input_language);
        	$sys_status_num = 2;
        } else {
        	$sys_status = $languagesObject->getLanguageString("error_2", $input_language);
        	$sys_status_num = 3;
        }

		$next  = array(				
			"0a" => "WALLET CREDIT", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
			"1" => $timeObject->reformatDate("j M, Y - g:i A", $input_date), //mine1.setDate(k.getString("1")); Eg: 2 Sep
			"2" => $pay_type, //mine1.setQuantityOrAmount(k.getString("2"));
			"3" => $transaction_id, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
			"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
			"5" => $total_amount_in_users_currency, //mine1.setTotalCharge(k.getString("5"));
			"6" => $input_date,
			"7" => "",
			"8" => "",
			"9" => $sys_status_num
		);


		$sys_credit_request_lastsku = $sku;
		array_push($sysResponse["news_returned"], $next);
	}
/***********************************************************************************************************

							CREDIT REQUESTS END

***********************************************************************************************************/

/***********************************************************************************************************

							WITHDRAWAL REQUESTS START

***********************************************************************************************************/
	if($input_withdrawal_request_lastsku == 0){
		$sys_withdrawal_request_sku_query_addition = "";
		$sys_withdrawal_request_query_values_array = array($input_id);
		$sys_withdrawal_request_query_values_types_string = "s";
	} else {
		$sys_withdrawal_request_sku_query_addition = " AND " . WITHDRAWAL_TABLE_NAME . ".sku < ? ";
		$sys_withdrawal_request_query_values_array = array($input_id, $input_credit_request_lastsku);
		$sys_withdrawal_request_query_values_types_string = "si";
	}

	$news_fetch_query =  "SELECT " 
	. WITHDRAWAL_TABLE_NAME . ".sku,  " 
	. WITHDRAWAL_TABLE_NAME . ".request_date,  " 
	. WITHDRAWAL_TABLE_NAME . ".pay_amt_usd,  " 
	. WITHDRAWAL_TABLE_NAME . ".account_name,  " 
	. WITHDRAWAL_TABLE_NAME . ".account_number,  " 
	. WITHDRAWAL_TABLE_NAME . ".routing_number,  " 
	. WITHDRAWAL_TABLE_NAME . ".pay_currency,  " 
	. WITHDRAWAL_TABLE_NAME . ".paid_status,  "
	. WITHDRAWAL_TABLE_NAME . ".bank_network_name,  " 
	. WITHDRAWAL_TABLE_NAME . ".withdrawal_country FROM "
	. WITHDRAWAL_TABLE_NAME . " "
	. " WHERE " . WITHDRAWAL_TABLE_NAME . ".investor_id = ? $sys_withdrawal_request_sku_query_addition ORDER BY  " . WITHDRAWAL_TABLE_NAME . ".sku DESC LIMIT 10";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($sys_credit_request_query_values_array), $sys_credit_request_query_values_types_string, $sys_credit_request_query_values_array);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		MONEY_CREDIT_TABLE_NAME . ".sku", 
		MONEY_CREDIT_TABLE_NAME . ".request_date", 
		MONEY_CREDIT_TABLE_NAME . ".pay_amt_usd", 
		MONEY_CREDIT_TABLE_NAME . ".account_name", 
		MONEY_CREDIT_TABLE_NAME . ".account_number", 
		MONEY_CREDIT_TABLE_NAME . ".routing_number", 
		MONEY_CREDIT_TABLE_NAME . ".pay_currency", 
		MONEY_CREDIT_TABLE_NAME . ".paid_status", 
		MONEY_CREDIT_TABLE_NAME . ".bank_network_name", 
		MONEY_CREDIT_TABLE_NAME . ".withdrawal_country"
	), 10, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($sku, $request_date, $pay_amt_usd, $account_name, $account_number, $routing_number, $pay_currency, $paid_status, $bank_network_name, $withdrawal_country);

	while($prepared_statement_results_array->fetch()){

		$info_1 = $bank_network_name . " : " . $withdrawal_country;

		if(trim($routing_number) == ""){
			$info_2 = $account_name . " : " . $account_number;
		} else {
			$info_2 = $account_name . " : (AC)" . $account_number . " : (RN)" . $routing_number;
		}

        if(trim($paid_status) == "completed"){
        	$sys_status = $languagesObject->getLanguageString("completed", $input_language);
        	$sys_status_num = 1;
        } else if (trim($paid_status) == "pending"){
        	$sys_status = $languagesObject->getLanguageString("pending", $input_language);
        	$sys_status_num = 0;
        } else if (trim($paid_status) == "cancelled"){
        	$sys_status = $languagesObject->getLanguageString("rejected", $input_language);
        	$sys_status_num = 2;
        } else {
        	$sys_status = $languagesObject->getLanguageString("error_2", $input_language);
        	$sys_status_num = 3;
        }

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$total_amount_in_users_currency = $input_my_currency . " " . $miscellaneousObject->convertPriceToNewCurrency($pay_currency, $pay_amt_usd, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$next  = array(				
			"0a" => "WITHDRAWAL", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
			"1" => $timeObject->reformatDate("j M, Y - g:i A", $request_date), //mine1.setDate(k.getString("1")); Eg: 2 Sep
			"2" => $info_2, //mine1.setQuantityOrAmount(k.getString("2"));
			"3" => $info_1, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
			"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
			"5" => $total_amount_in_users_currency, //mine1.setTotalCharge(k.getString("5")); 
			"6" => $request_date,
			"7" => "",
			"8" => "",
			"9" => $sys_status_num
		);


		$sys_withdrawal_request_lastsku = $sku;
		array_push($sysResponse["news_returned"], $next);
	}
/***********************************************************************************************************

							WITHDRAWAL REQUESTS END

***********************************************************************************************************/

/***********************************************************************************************************

							TRANSFER START

***********************************************************************************************************/

	if($input_transfer_last_sku == 0){
		$sys_transfer_sku_query_addition = "";
		$sys_transfer_query_values_array = array($input_id, $input_id);
		$sys_transfer_query_values_types_string = "ss";
	} else {
		$sys_transfer_sku_query_addition = " AND " . SHARES_TRANSFER_TABLE_NAME . ".sku < ? ";
		$sys_transfer_query_values_array = array($input_id, $input_id, $input_transfer_last_sku);
		$sys_transfer_query_values_types_string = "ssi";
	}


	$news_fetch_query =  "SELECT " 
	. SHARES_TRANSFER_TABLE_NAME . ".sku,  " 
	. SHARES_TRANSFER_TABLE_NAME . ".date_time,  " 
	. SHARES_TRANSFER_TABLE_NAME . ".num_shares_transfered,  "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id,  " 
	. SHARES_HOSTED_TABLE_NAME . ".share_name,  " 
	. SHARES_TRANSFER_TABLE_NAME . ".admin_review_status,  " 
	. USER_BIO_TABLE_NAME . ".currency,  " 
	. USER_BIO_TABLE_NAME . ".pot_name,  " 
	. SHARES_TRANSFER_TABLE_NAME . ".sender_id,  " 
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id FROM "  
	. SHARES_TRANSFER_TABLE_NAME . " INNER JOIN " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " ON  "  
	. SHARES_TRANSFER_TABLE_NAME . ".share_id="  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id INNER JOIN "
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id="  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id INNER JOIN "
	. USER_BIO_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. SHARES_TRANSFER_TABLE_NAME . ".receiver_id "
	. " WHERE ( " . SHARES_TRANSFER_TABLE_NAME . ".receiver_id = ? OR " . SHARES_TRANSFER_TABLE_NAME . ".sender_id = ? ) AND " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type != 'sale' $sys_transfer_sku_query_addition ORDER BY  " . SHARES_TRANSFER_TABLE_NAME . ".sku DESC LIMIT 10";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($sys_transfer_query_values_array), $sys_transfer_query_values_types_string, $sys_transfer_query_values_array);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		SHARES_TRANSFER_TABLE_NAME . ".sku", 
		SHARES_TRANSFER_TABLE_NAME . ".date_time", 
		SHARES_TRANSFER_TABLE_NAME . ".num_shares_transfered", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id", 
		SHARES_HOSTED_TABLE_NAME . ".share_name", 
		SHARES_TRANSFER_TABLE_NAME . ".admin_review_status", 
		USER_BIO_TABLE_NAME . ".currency", 
		USER_BIO_TABLE_NAME . ".pot_name", 
		SHARES_TRANSFER_TABLE_NAME . ".sender_id", 
		SHARES_HOSTED_TABLE_NAME . ".parent_shares_id"
	), 10, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($sku, $date_time, $item_quantity, $share_id, $share_name, $admin_review_status, $currency, $pot_name, $sender_id, $parent_shares_id);

	while($prepared_statement_results_array->fetch()){

        $sys_status_num = intval($admin_review_status);

        if(intval($admin_review_status) == 1){
        	$sys_status = $languagesObject->getLanguageString("completed", $input_language);
        } else if (intval($admin_review_status) == 0){
        	$sys_status = $languagesObject->getLanguageString("pending", $input_language);
        } else if (intval($admin_review_status) == 2){
        	$sys_status = $languagesObject->getLanguageString("rejected", $input_language);
        } else {
        	$sys_status = $languagesObject->getLanguageString("error_2", $input_language);
        	$sys_status_num = 3;
        }



		if($sender_id == $input_id){
			$next  = array(				
				"0a" => "SHARES TRANSFER OUT", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
				"1" => $timeObject->reformatDate("j M, Y - g:i A", $date_time), //mine1.setDate(k.getString("1")); Eg: 2 Sep
				"2" => strval($item_quantity), //mine1.setQuantityOrAmount(k.getString("2"));
				"3" => $share_name, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
				"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
				"5" => "", //mine1.setTotalCharge(k.getString("5"));
				"6" => $date_time,
				"7" => $pot_name,
				"8" => $parent_shares_id,
				"9" => $sys_status_num
			);
		} else {
		$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT pot_name FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($sender_id));

		if($prepared_statement2 === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement2, array("pot_name"), 1, 1);

		if($prepared_statement_results_array2 !== false && $prepared_statement_results_array2[0] != "pot_name" && $prepared_statement_results_array2[0] != USER_BIO_TABLE_NAME . "pot_name"){
			$pot_name = $prepared_statement_results_array2[0];
		} else {
			$pot_name = "";
		}



			$next  = array(				
				"0a" => "SHARES TRANSFER IN", //mine1.setType(k.getString("0a")); Eg: TRANSFER, SHARES SALE, WITHDRAWAL, CREDIT
				"1" => $timeObject->reformatDate("j M, Y - g:i A", $date_time), //mine1.setDate(k.getString("1")); Eg: 2 Sep
				"2" => strval($item_quantity), //mine1.setQuantityOrAmount(k.getString("2"));
				"3" => $share_name, //mine1.setItemNameOrReceiveNumberOrCreditType(k.getString("3"));
				"4" => $sys_status, //mine1.setStatusOrBuyerName(k.getString("4"));
				"5" => "", //mine1.setTotalCharge(k.getString("5"));
				"6" => $date_time,
				"7" => $pot_name,
				"8" => $parent_shares_id,
				"9" => $sys_status_num
			);
		}

		$sys_transfer_last_sku = $sku;
		array_push($sysResponse["news_returned"], $next);
	}

/***********************************************************************************************************

							SALES / ADETOR END

***********************************************************************************************************/

	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "",  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on,
		'8' => strval($sys_sales_lastsku),
		'9' => strval($sys_credit_request_lastsku),
		'10' => strval($sys_withdrawal_request_lastsku),
		'11' => strval($sys_transfer_last_sku)
		);

	usort($sysResponse["news_returned"], 'date_compare');

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

function date_compare($a, $b)
{
    $t1 = strtotime($a['6']);
    $t2 = strtotime($b['6']);
    return $t2 - $t1;
}    


