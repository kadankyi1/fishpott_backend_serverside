<?php
session_start();
$error_page = "../../abanfo/in/examples/_1add_new_share_value.php";

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


	if(isset($_POST["share_parent_id"]) && trim($_POST["share_parent_id"]) != ""){
		$var_share_parent_id = trim($_POST["share_parent_id"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	if(isset($_POST["total_dividends"]) && floatval($_POST["total_dividends"]) >= 0){
		$var_total_dividends = floatval($_POST["total_dividends"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_POST["share_value"]) && floatval($_POST["share_value"]) >= 0){
		$var_share_value = floatval($_POST["share_value"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_POST["max_share_value"]) && floatval($_POST["max_share_value"]) >= 0){
		$var_max_share_value = floatval($_POST["max_share_value"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$var_share_dividend = 0;
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

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT value_per_share, dividend_per_share, parent_id  FROM " . SHARES_VALUE_HISTORY_TABLE_NAME . " WHERE parent_id = ? ORDER BY sku DESC", 1, "s", array($var_share_parent_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("value_per_share", "dividend_per_share", "parent_id"), 3, 1);

	if($prepared_statement_results_array === false){

			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	if($prepared_statement_results_array[2] == "" || $prepared_statement_results_array[2] == "parent_id"){
		$var_old_value_per_share = 0;
		$var_old_dividend_per_share = 0;
	} else {
		// IF THE DATABASE QUERY GOT NO RESULTS
		$var_old_value_per_share = $prepared_statement_results_array[0];
		$var_old_dividend_per_share = $prepared_statement_results_array[1];
	}



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT share_name, company_pottname, shares_logo, total_number  FROM " . SHARES_HOSTED_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($var_share_parent_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("share_name", "company_pottname", "shares_logo", "total_number"), 4, 1);

	if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if($prepared_statement_results_array[0] == "" || $prepared_statement_results_array[0] == "share_name" || $prepared_statement_results_array[3] <= 0 ){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	// IF THE DATABASE QUERY GOT NO RESULTS
	$var_share_name = $prepared_statement_results_array[0];
	$var_share_company_pottname = $prepared_statement_results_array[1];
	$var_shares_logo = $prepared_statement_results_array[2];
	$var_total_hosted_shares = $prepared_statement_results_array[3];

	$var_share_dividend = $var_total_dividends/$var_total_hosted_shares;


    $var_shares_logo = "../../user/" . $var_shares_logo; 
    if(trim($prepared_statement_results_array[2]) != "" && $validatorObject->fileExists($var_shares_logo) !== false){
        $var_shares_logo = HTTP_HEAD . "://fishpott.com/user/" . $prepared_statement_results_array[2];
    } else {
        $var_shares_logo = "fp";
    }


$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT count(DISTINCT(owner_id))  FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($var_share_parent_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(DISTINCT(owner_id))"), 1, 1);

	if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS

	$var_investors_now = $prepared_statement_results_array[0];


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_HOSTED_TABLE_NAME . " SET value_per_share = ?, yield_per_share = ?, curr_max_price = ?, last_dividend_pay_date = ? WHERE parent_shares_id = ?", 5, "dddss", array($var_share_value, $var_share_dividend, $var_max_share_value, date('Y-m-d H:i:s'), $var_share_parent_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update share information. If this continues, inform Super Admin");
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "INSERT INTO " . SHARES_VALUE_HISTORY_TABLE_NAME . " (parent_id, value_record_date, value_per_share, dividend_per_share, investors_now) VALUES (?, ?, ?, ?, ?)" , 5, "ssddi", array($var_share_parent_id, date("Y-m-d"), $var_share_value, $var_share_dividend, $var_investors_now));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$user_language = "en";
	
	if($var_share_value > $var_old_value_per_share){
		$sys_status_info = "Investment Value Increased";
		$sys_status_info2 =  $var_share_name . $languagesObject->getLanguageString("has_increased_in_value", $user_language);
	} else if($var_share_dividend > $var_old_value_per_share){
		$sys_status_info = "Investment Yield Value Increased";
		$sys_status_info2 =  $var_share_name . $languagesObject->getLanguageString("yield_per_share_has_increased_in_value", $user_language);
	} else {
		$sys_status_info = "Investment Yield Value Unchanged";
		$sys_status_info2 =  $var_share_name . $languagesObject->getLanguageString("has_changes_in_its_value_dividend", $user_language);
	}


$query =  "SELECT " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id,  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares ,  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id FROM "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE parent_shares_id = ?";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($var_share_parent_id));

    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("owner_id", "num_of_shares", "share_id"), 3, 2);

    //BINDING THE RESULTS TO VARIABLES
    $prepared_statement_results_array->bind_result($owner_id, $num_of_shares, $share_id);

    while($prepared_statement_results_array->fetch()){

	if(trim($owner_id) == ""){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT investor_id, fcm_token, fcm_token_ios, withdrawal_wallet_usd,  currency FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($owner_id));

	if($prepared_statement2 === false){
		continue;
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement2, array("investor_id", "fcm_token", "fcm_token_ios", "withdrawal_wallet_usd", "currency"), 5, 1);

	if($prepared_statement_results_array2 === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if($prepared_statement_results_array2[0] == "" || $prepared_statement_results_array2[0] == "investor_id"){
		continue;
	}


	// IF THE DATABASE QUERY GOT NO RESULTS
	$sys_receiver_fcm_token = $prepared_statement_results_array2[1];
	$sys_receiver_fcm_token_ios = $prepared_statement_results_array2[2];
	$sys_withdrawal_wallet_usd = $prepared_statement_results_array2[3];
	$sys_currency = $prepared_statement_results_array2[4];
	if(trim($sys_currency) == ""){
		$sys_currency = "USD";
	}
	$total_payout_with_fp_commission = $num_of_shares * $var_share_dividend;
	$fp_commission = $total_payout_with_fp_commission * FISHPOTT_YIELD_PROCESSING_PERCENTAGE;


	$total_payout_without_fp_commission = $total_payout_with_fp_commission - $fp_commission;

	$sys_new_withdrawal_wallet_usd = round(($sys_withdrawal_wallet_usd + $total_payout_without_fp_commission),2,PHP_ROUND_HALF_DOWN);

	$receiver_keys = array();
	if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
		$receiver_keys[0] = $sys_receiver_fcm_token;
	}

	if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
		$receiver_keys[count($receiver_keys)] = $sys_receiver_fcm_token_ios;
	}

	$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . USER_BIO_TABLE_NAME . " SET withdrawal_wallet_usd = ? WHERE investor_id = ?", 2, "ds", array($sys_new_withdrawal_wallet_usd, $owner_id));

	if($prepared_statement2 === false){
		 continue;
	}

	$payment_datetime = date('Y-m-d H:i:s');
	$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET last_payment_date = ? WHERE share_id = ?", 2, "ss", array($payment_datetime, $share_id));

	if($prepared_statement2 === false){
		 continue;
	}

		$transaction_id = $payment_datetime . "_" . $share_id;

		$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "INSERT INTO " . MONEY_CREDIT_TABLE_NAME . " (
				done_status,
				pay_type,
				reviewer_admin_id,
		 		currency_sent,
		 		amount_sent,
		 		transaction_id, 
		 		sender_name, 
		 		investor_id, 
		 		send_date, 
		 		input_date
		 	) 
		 		VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" ,
			10, 
			"ssssdsssss", 
		    array(
		    	"completed",
			  	"DIVIDEND", 
			  	$var_admin_id,
			  	"USD", 
			  	$total_payout_without_fp_commission, 
			  	$transaction_id, 
			  	$var_share_name, 
			  	$owner_id, 
			  	$payment_datetime, 
			  	date("Y-m-d H:i:s")
			)
		);

		if($prepared_statement2 === false){
			continue;
		}


	$sys_total_dividends_my_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $total_payout_without_fp_commission, $sys_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);


		$sys_status_info = "Dividends Paid";
		$sys_status_info2 = $var_share_name . " has paid " . $sys_currency . $sys_total_dividends_my_currency . " as dividends";

	$miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		$var_shares_logo, 
		"normal", 
		"general_notification", 
		"shares_value", 
		$var_share_parent_id, 
		$var_share_company_pottname, 
		$sys_status_info, 
		$sys_status_info2, 
		date("F j, Y"), 
		""
	);

}

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
	
