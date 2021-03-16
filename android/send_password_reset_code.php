<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["phone"]) && trim($_POST["phone"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_POST["phone"]) && trim($_POST["phone"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_PHONES);
		if (in_array(trim($_POST["phone"]), $ALL_DEVELOPER_POTTNAMES)){
			$GLOBALS["USAGE_MODE_IS_LIVE"] = DEVELOPER_USING_LIVE_MODE;
		}
	}
	

	//CALLING THE INPUT VALIDATOR CLASS
	include_once 'classes/input_validation_class.php';
	//CALLING THE MISCELLANOUS CLASS
	include_once 'classes/miscellaneous_class.php';
	//CALLING TO THE DATABASE CLASS
	include_once 'classes/db_class.php';
	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once 'classes/prepared_statement_class.php';
	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once 'classes/languages_class.php';
	//CALLING TO THE TIME OPERATOR CLASS
	include_once 'classes/time_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["phone"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();
	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObjects = new languagesActions();
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();
	// CREATING TIME OPERATOR OBJECT
	$timeOperatorObject = new timeOperator();

	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15)){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_phone) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($input_phone,1,strlen($input_phone))) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($input_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password_reset_date, password_reset_code, flag FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password_reset_date", "password_reset_code", "flag"), 3, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}
	
	if(trim($prepared_statement_results_array[0]) == "password_reset_date" || trim($prepared_statement_results_array[1]) == "password_reset_code"){
		$miscellaneousObject->respondFrontEnd1("yes", $languagesObjects->getLanguageString("password_reset_instructions_sent_to_phone_number", $input_language));
	}

	if($prepared_statement_results_array[2] != 0){
			$miscellaneousObject->respondFrontEnd1("0", $languagesObjects->getLanguageString("your_account_has_been_suspended", $input_language));
	}

	if(trim($prepared_statement_results_array[0]) != "" || trim($prepared_statement_results_array[1]) != ""){
		//GETTING THE DIFFERENCE IN TIME BETWEEN RESET DATE AND TODAYS DATE
		$time_difference = $timeOperatorObject->getDateDifference($prepared_statement_results_array[0], date("Y-m-d H:i:s"));
		if($time_difference === false){
			$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
		}

		if($time_difference["year_difference"] >= 1 || $time_difference["month_difference"] >= 1 || $time_difference["day_difference"] >= 1){
			$time_difference = 1;
		} else {
			$time_difference = 0;
		}
	} else {
		$time_difference = 1;
	}


	// WHEN THERE IS NO RESET CODE AND RESET DATE IN THE DB, THEN WE WILL SEND THE CODE
	// WHEN THERE IS NO RESET CODE AND THE DATE IS ONE DAY PAST, THEN WE WILL SEND THE CODE
	if(($prepared_statement_results_array[1] == "" && $prepared_statement_results_array[0] == "") || $time_difference == 1){
		// GENERATING RESET CODE AND UPDATING THE RESET DATE, THE PASSWORD 
		$reset_code = $miscellaneousObject->getRandomString(9);

		///SEND THE RESET CODE HERE
		$reset_text = "FishPott - " . $languagesObjects->getLanguageString("your_password_reset_code_is", $input_language) . " " . $reset_code;
		$receiver_number = substr($input_phone,1,strlen($input_phone));


		$sms_response = $miscellaneousObject->sendSMS('sendsms', SMS_G_USERNAME, SMS_G_PASS, SENDER_NAME, $receiver_number, $reset_text);

		if($sms_response != 1){
			$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
		}

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET password_reset_date = ?,  password_reset_code = ? WHERE number_login = ?", 3, "sss", array(date("Y-m-d H:i:s"), $reset_code, $input_phone));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
		}

		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		$miscellaneousObject->respondFrontEnd1("yes", $languagesObjects->getLanguageString("password_reset_code_sent", $input_language));

	} else if($prepared_statement_results_array[1] != ""){
		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		$miscellaneousObject->respondFrontEnd1("yes", $languagesObjects->getLanguageString("use_pending_code_or_wait_24_hours", $input_language));
	} else {
		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		$miscellaneousObject->respondFrontEnd1($languagesObjects->getLanguageString("error", $input_language), $languagesObjects->getLanguageString("request_failed", $input_language));
	}




}