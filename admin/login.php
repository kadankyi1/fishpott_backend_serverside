<?php
session_start();
$error_page = "../../abanfo/index.php?error";

	//CALLING THE CONFIGURATION FILE
	require_once("../android/config.php");
	//CALLING THE INPUT VALIDATOR CLASS
	include_once '../android/classes/input_validation_class.php';
	//CALLING THE MISCELLANOUS CLASS
	include_once '../android/classes/miscellaneous_class.php';
	//CALLING TO THE DATABASE CLASS
	include_once '../android/classes/db_class.php';
	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once '../android/classes/prepared_statement_class.php';
	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once '../android/classes/languages_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["phone"]);
	$input_password = trim($_POST["password"]);

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();

	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15) === false || $validatorObject->stringIsNotMoreThanMaxLength($input_password, 20) === false){

		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_phone) !== true){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($input_phone,1,strlen($input_phone))) !== true){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($input_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT flag, admin_password, admin_id, admin_name, admin_country, admin_profile_pic, admin_level FROM " . ADMIN_BIO_LOGIN_TABLE_NAME . " WHERE admin_phone = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("flag", "admin_password", "admin_id", "admin_name", "admin_country", "admin_profile_pic", "admin_level"), 7, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	$input_password_hashed = $validatorObject->hashString($input_password);

	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//CHECKING THAT THE PASSWORD MATCHES AND THE ACCOUNT IS NOT FLAGGED
	if($input_password_hashed == $prepared_statement_results_array[1] && $prepared_statement_results_array[0] == 0){

		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$_SESSION["admin_pass"] = $prepared_statement_results_array[1];
		$_SESSION["admin_id"] = $prepared_statement_results_array[2];
		$_SESSION["admin_name"] = $prepared_statement_results_array[3];
		$_SESSION["admin_country"] = $prepared_statement_results_array[4];
		$_SESSION["admin_level"] = $prepared_statement_results_array[6];
		$_SESSION["admin_phone"] = $input_phone;
		$db_profile_picture = "../../pic_upload/" . $prepared_statement_results_array[5];

		$_SESSION["admin_currency"] = $miscellaneousObject->getCurrencyForUIFromCountry($prepared_statement_results_array[4]);

		if($prepared_statement_results_array[5] != "" && $validatorObject->fileExists($db_profile_picture) !== false){
			$_SESSION["admin_profile_pic"] = HTTP_HEAD . "://fishpott.com/pic_upload/" . $prepared_statement_results_array[5];
		} else {
			$_SESSION["admin_profile_pic"] =  FISHPOTT_APP_ICON_PICTURE_LINK;
		}
		$miscellaneousObject->respondFrontEnd2("red", "../../abanfo/in/examples/?success", $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));

	} else if($input_password_hashed != $prepared_statement_results_array[1]){
			$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));
	} else if($prepared_statement_results_array[0] != 0){
			$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	} else {
			$miscellaneousObject->respondFrontEnd2("red", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	
