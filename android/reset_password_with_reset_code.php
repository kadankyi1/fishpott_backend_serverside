<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["phone"]) && trim($_POST["phone"]) != "" &&
	isset($_POST["new_password"]) && trim($_POST["new_password"]) != "" &&
	isset($_POST["reset_code"]) && trim($_POST["reset_code"]) != "" &&
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
	//CALLING TO THE TIME CLASS
	include_once 'classes/time_class.php';


	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["phone"]);
	$input_new_password = trim($_POST["new_password"]);
	$input_reset_code = trim($_POST["reset_code"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();
	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();
	// CREATING TIME OPERATOR OBJECT
	$timeOperatorObject = new timeOperator();
	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET

	// MAKING SURE THE PHONE NUMBER, PASSWORD AND RESET CODE CONFORM TO MAXLENGTH
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15) || $validatorObject->stringIsNotMoreThanMaxLength($input_new_password, 20) || $validatorObject->stringIsNotMoreThanMaxLength($input_reset_code, 10)){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER AND THE RESET CODE CONTAINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_phone) !== true || $validatorObject->stringContainsNoTags($input_reset_code) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($input_phone,1,strlen($input_phone))) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($input_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password_reset_date, password_reset_code, flag FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password_reset_date", "password_reset_code", "flag"), 3, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "password_reset_date" || trim($prepared_statement_results_array[1]) == "password_reset_code"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){
		$miscellaneousObject->respondFrontEnd1("0", $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	}
	// HASHING THE NEW PASSWORD
	$input_new_password_hashed = $validatorObject->hashString($input_new_password);

	//GETTING THE DIFFERENCE IN TIME BETWEEN RESET DATE AND TODAYS DATE
	$time_difference = $timeOperatorObject->getDateDifference($prepared_statement_results_array[0], date("Y-m-d H:i:s"));
	if($time_difference === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// CHECKING THAT RESET CODE HAS NOT EXPIRED. IT EXPIRES AFTER 3 DAYS
	if($time_difference["year_difference"] < 1 && $time_difference["month_difference"] < 1 && $time_difference["day_difference"] < 3){
		$code_has_expired = false;
	} else {
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("the_reset_code_has_expired", $input_language));		
	}
	
	if($prepared_statement_results_array[1] == $input_reset_code && $code_has_expired === false){
		// UPDATING THE RESET DATE, THE PASSWORD
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET password_reset_date = ?,  password_reset_code = '',  password = ? WHERE number_login = ?", 3, "sss", array(date("Y-m-d H:i:s"), $input_new_password_hashed, $input_phone));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		
		$miscellaneousObject->respondFrontEnd1("yes", $languagesObject->getLanguageString("your_password_has_been_changed_successfully", $input_language));
	} else if($prepared_statement_results_array[1] != $input_reset_code){
		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	} else {
		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

}