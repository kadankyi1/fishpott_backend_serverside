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

	if(isset($_POST["share_dividend"]) && floatval($_POST["share_dividend"]) >= 0){
		$var_share_dividend = floatval($_POST["share_dividend"]);
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



	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT share_name, company_pottname, shares_logo  FROM " . SHARES_HOSTED_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($var_share_parent_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("share_name", "company_pottname", "shares_logo"), 3, 1);

	if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if($prepared_statement_results_array[0] == "" || $prepared_statement_results_array[0] == "share_name"){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	// IF THE DATABASE QUERY GOT NO RESULTS
	$var_share_name = $prepared_statement_results_array[0];
	$var_share_company_pottname = $prepared_statement_results_array[1];
	$var_shares_logo = $prepared_statement_results_array[2];

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


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_HOSTED_TABLE_NAME . " SET value_per_share = ?, yield_per_share = ?, curr_max_price = ? WHERE parent_shares_id = ?", 4, "ddds", array($var_share_value, $var_share_dividend, $var_max_share_value, $var_share_parent_id));

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



	$miscellaneousObject->sendNotificationToTopic(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		ALL_USERS_TOPIC, 
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
	
