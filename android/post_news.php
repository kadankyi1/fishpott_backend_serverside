<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["news_text"]) &&
	isset($_POST["news_time"]) && trim($_POST["news_time"]) != "" &&
	isset($_POST["added_item_id"]) &&
	isset($_POST["added_item_price"]) &&
	isset($_POST["added_item_quantity"]) &&
	isset($_POST["myrawpass"]) && 
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
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
	$input_news_text = trim($_POST["news_text"]);
	$input_news_time = trim($_POST["news_time"]);
	$input_added_item_id = trim($_POST["added_item_id"]);
	$input_added_item_price = floatval($_POST["added_item_price"]);
	$input_added_item_quantity = intval($_POST["added_item_quantity"]);
	$input_myrawpass = trim($_POST["myrawpass"]);

	if(isset($_POST["news_reposted_id"]) && trim($_POST["news_reposted_id"]) != ""){
		$input_news_reposted_id = trim($_POST["news_reposted_id"]);
	}

	$input_mypottname = trim($_POST["mypottname"]);
	$input_my_currency = trim($_POST["my_currency"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	//DECLARING THE ARRAY FOR THE RESULTS
	$sys_image_added = false;
	$sys_image_was_received = false;
	$sys_sharesforsale_added_or_updated = false;
	$sys_added_item_type = "shares4sale";
	$input_my_currency = $miscellaneousObject->getCurrencyAbreviationsFromSymbols($input_my_currency);
	$sys_sharesforsale_id = "";

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

	if($input_myrawpass != "" && $validatorObject->stringIsNotMoreThanMaxLength($input_myrawpass, 20) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_mypottname) !== true){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("login_failed_if_this_continues_uninstall_your_app_reinstall_and_login_again", $input_language));
	}

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

	$sys_dbpass = $prepared_statement_results_array[0];

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

							GETTING USER'S PROFILE PICTURE

***********************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT profile_picture FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("profile_picture"), 1, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] == "profile_picture" ){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_profile_picture = $prepared_statement_results_array[0];

	if(trim($sys_profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $sys_profile_picture) !== false){
		$sys_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $sys_profile_picture;
	} else {
		$sys_profile_picture = "";
	}




	//CREATING NEWS ID
	$sys_news_id = "news_" . $input_mypottname . $miscellaneousObject->getRandomString(50) . $miscellaneousObject->getRandomStringFromDateTime();

	//UPLOADING NEWS VIDEO
	if(isset($_FILES["news_video"])){

		$input_pott_pic = $_FILES["news_video"];

		// GETTING THE BASE NAME OF THE INPUT FILE
		$sys_input_file_type = $input_pott_pic["type"];
		$sys_input_file_basename = basename($input_pott_pic["name"]);
		$sys_input_file_extension = strtolower(pathinfo($sys_input_file_basename,PATHINFO_EXTENSION));

		if($sys_input_file_extension == "mp4" || $sys_input_file_extension == "mkv" || $sys_input_file_extension == "ogg"){

			if($fileObject->fileSizeIsNotLargerThanMaxSize($input_pott_pic, MAXIMUM_ALLOWED_NEWS_VIDEO_UPLOAD_SIZE) !== false){
				$sys_video_name = $input_id . $miscellaneousObject->getRandomString(50) . $miscellaneousObject->getRandomStringFromDateTime() . "." . $sys_input_file_extension;

				$sys_video_name_for_upload = "../../user/news_files/videos/" . $sys_video_name;
				$sys_video_name_for_database = "news_files/videos/" . $sys_video_name;

				if($fileObject->moveFile($input_pott_pic["tmp_name"], $sys_video_name_for_upload) === false){
					$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
				}
			} else {
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}

		} else {
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}


	} else {
		$sys_video_name_for_database = "";
	}

	//UPLOADING AND POSTING ALL IMAGES
	for ($i=1; $i < 11; $i++) { 
		$current_file_index = "news_image_" . strval($i);

		if(isset($_FILES[$current_file_index])){
			$sys_image_was_received = true;
			

			/*
			$input_pott_pic = $_FILES[$current_file_index];

			$sys_input_file_type = $input_pott_pic["type"];
			$sys_input_file_basename = basename($input_pott_pic["name"]);
			$sys_input_file_extension = strtolower(pathinfo($sys_input_file_basename,PATHINFO_EXTENSION));
			
			$sys_image_name = $current_file_index . "." . $sys_input_file_extension;
			$sys_image_name_for_upload = "../../temp/" . $sys_image_name;

			if($fileObject->moveFile($input_pott_pic["tmp_name"], $sys_image_name_for_upload) === false){
				continue;	
			}
			*/

			$input_pott_pic = $_FILES[$current_file_index];

			// GETTING THE BASE NAME OF THE INPUT FILE
			$sys_input_file_type = $input_pott_pic["type"];
			$sys_input_file_basename = basename($input_pott_pic["name"]);
			$sys_input_file_extension = strtolower(pathinfo($sys_input_file_basename,PATHINFO_EXTENSION));

			// CHECKING THE UPLOAD FILE TYPE TO BE SURE IF NOT WE FAIL THE PROCESS
			if($input_pott_pic["type"] != "image/jpeg" && $input_pott_pic["type"] != "image/png"){
				continue;	
			}

			if($sys_input_file_extension != "jpg" && $sys_input_file_extension != "png" && $sys_input_file_extension != "jpeg"){
				continue;	
			}


			if($fileObject->fileSizeIsNotLargerThanMaxSize($input_pott_pic, MAXIMUM_ALLOWED_NEWS_IMAGE_UPLOAD_SIZE) === false){
				continue;	
			}

			$sys_image_name = $input_id . $miscellaneousObject->getRandomString(50) . $miscellaneousObject->getRandomStringFromDateTime() . "." . $sys_input_file_extension;

			$sys_image_name_for_upload = "../../user/news_files/pics/" . $sys_image_name;
			$sys_image_name_for_database = "news_files/pics/" . $sys_image_name;

			if($fileObject->moveFile($input_pott_pic["tmp_name"], $sys_image_name_for_upload) === false){
				continue;	
			}

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . NEWS_IMAGES_TABLE_NAME . " (news_id, maker_id, link_address, flag) VALUES (?, ?, ?, ?)" , 4, "sssi", array($sys_news_id, $input_id, $sys_image_name_for_database, 0));
			$sys_image_added = true;


		}

	}

	$sys_image_was_received_string = "";
	if($sys_image_was_received){
		if($sys_image_added === false){
			$sys_image_was_received_string = "";
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		} else {
			$sys_image_was_received_string = "news_image_was_received";
		}
	}

	//ADDING ADDED ITEM -- AS SHARES FOR SALE IF IT APPLIES
	
	if($input_added_item_id != "" && $input_added_item_price > 0 && $input_added_item_quantity > 0){

		if($sys_dbpass != $validatorObject->hashString($input_myrawpass)){		
			$miscellaneousObject->respondFrontEnd3(5, $languagesObject->getLanguageString("incorrect_password", $input_language));
		}

		// MAKING SURE THE PERSON POSTING OWNS THE SHARES AND THE NUMBER HE IS SELLING
		$news_fetch_query =  "SELECT "  
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares, "
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id FROM "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
		. SHARES_HOSTED_TABLE_NAME . " ON  "  
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id="  
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id "
		. " WHERE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ? AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".flag = 0 AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0 AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares >= ?  AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id = ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 3, "sis", array($input_id, $input_added_item_quantity, $input_added_item_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("num_of_shares", "parent_shares_id"), 2, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// IF THE DATABASE QUERY GOT NO RESULTS
		if(intval($prepared_statement_results_array[0]) <= 0){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		$sys_parent_shares_id = $prepared_statement_results_array[1];

		// CHECKING IF THE SHARES GOING ON SALE IS ALREADY ON SALE
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT currency FROM " . SHARES4SALE_TABLE_NAME . " WHERE shares4sale_owner_id = ? AND sharesOnSale_id = ? AND flag = 0", 2, "ss", array($input_id, $input_added_item_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("currency"), 1, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

	   if(trim($prepared_statement_results_array[0]) == "currency" || trim($prepared_statement_results_array[0]) == ""){
			// IF THE DATABASE QUERY GOT NO RESULTS, THEN WE ADD NEW SHARES FOR SALE
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . SHARES4SALE_TABLE_NAME . " (parent_shares_id, shares4sale_owner_id, sharesOnSale_id, selling_price, currency, num_on_sale) VALUES ( ?, ?, ?, ?, ?, ?)" , 6, "sssdsi", array($sys_parent_shares_id, $input_id, $input_added_item_id, $input_added_item_price, $input_my_currency, $input_added_item_quantity));

			if($prepared_statement === false){
			  $miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
			}
			$sys_sharesforsale_added_or_updated = true;
		} else {
			// WE UPDATE THE OLD SHARES FOR SALE INFO
			$all_news_connected_to_shares4sale = $sys_news_id . " " . $prepared_statement_results_array[0];
			
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . SHARES4SALE_TABLE_NAME . " SET currency = ?, selling_price = ?, num_on_sale = ? WHERE shares4sale_owner_id = ? AND sharesOnSale_id = ?", 5, "sdiss", array($input_my_currency, $input_added_item_price, $input_added_item_quantity, $input_id, $input_added_item_id));
			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
			}
			$sys_sharesforsale_added_or_updated = true;
		}


	}
	
	//CHECKING IF NEWS IS A SHARES-FOR-SALE OR A NEWS WITH AN ADDED ITEM
	if($sys_sharesforsale_added_or_updated && (isset($_FILES["news_video"]) || $sys_image_added === true)){
		$news_type = "shares4sale";
		$sys_sharesforsale_id = $input_added_item_id;
		$input_added_item_id = "";
		$sys_added_item_type = "";
	} elseif($sys_sharesforsale_added_or_updated && !isset($_FILES["news_video"]) && $sys_image_added === false)  {
		$news_type = "news";
	} else {
		$news_type = "news";
		$input_added_item_id = "";
		$sys_added_item_type = "";
	}

	//INSERTING NEWS TO NEWSFEED TABLE DATABASE
	if(isset($input_news_reposted_id) && trim($input_news_reposted_id) != ""){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . NEWS_TABLE_NAME . " (news_id, type, inputtor_id, date_time, received_server_datetime, news, news_id_ref, added_item_type, added_item_news_id, shares4sale_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" , 10, "ssssssssss", array($sys_news_id, "shared_news", $input_id, $input_news_time, date("Y-m-d H:i:s"), $input_news_text, $input_news_reposted_id, $sys_added_item_type, $input_added_item_id, $sys_sharesforsale_id));

		if($prepared_statement === false){
		  $miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
	} else {
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . NEWS_TABLE_NAME . " (news_id, type, inputtor_id, date_time, received_server_datetime, news, news_video, added_item_type, added_item_news_id, shares4sale_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" , 10, "ssssssssss", array($sys_news_id, $news_type, $input_id, $input_news_time, date("Y-m-d H:i:s"), $input_news_text, $sys_video_name_for_database, $sys_added_item_type, $input_added_item_id, $sys_sharesforsale_id));

		if($prepared_statement === false){
		  $miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
	}

	if($input_news_text != ""){
		//GETTING ALL MENTIONS AND SENDING NOTIFICATIONS
		$sys_all_mentions_array_result = $miscellaneousObject->getAllMentionsAsArrayFromNews($input_news_text);
		$sys_all_mentions_array = $sys_all_mentions_array_result["mentions"];
		$sys_all_mentions_question_marks = $sys_all_mentions_array_result["mentions_question_marks"];
		$sys_all_mentions_value_type_strings = $sys_all_mentions_array_result["mentions_value_type_strings"];


		if (in_array("mylinkups", $sys_all_mentions_array) || in_array("Mylinkups", $sys_all_mentions_array) || in_array("MYLINKUPS", $sys_all_mentions_array)){
			$news_fetch_query =  "SELECT "  
			. USER_BIO_TABLE_NAME . ".pot_name FROM "
			. USER_BIO_TABLE_NAME . " INNER JOIN " 
			. LINKUPS_TABLE_NAME . " ON  "  
			. USER_BIO_TABLE_NAME . ".investor_id="  
			. LINKUPS_TABLE_NAME . ".sender_id "
			. " WHERE " . LINKUPS_TABLE_NAME . ".receiver_id = ? AND " . LINKUPS_TABLE_NAME . ".status = 1 ORDER BY " .  LINKUPS_TABLE_NAME . ".sku DESC";

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s", array($input_id));

			if($prepared_statement !== false){
				// GETTING RESULTS OF QUERY INTO AN ARRAY
				$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
					USER_BIO_TABLE_NAME . ".pot_name"
				), 1, 2);

				//BINDING THE RESULTS TO VARIABLES
				$prepared_statement_results_array->bind_result($pot_name);
			    while($prepared_statement_results_array->fetch()){
			    	$sys_all_mentions_array[count($sys_all_mentions_array)] = $pot_name;
			    	$sys_all_mentions_question_marks .= ",?";
			    	$sys_all_mentions_value_type_strings .= "s";
				}
				if(substr($sys_all_mentions_question_marks,strlen($sys_all_mentions_question_marks)-1) == ","){
					$sys_all_mentions_question_marks = substr($sys_all_mentions_question_marks,0,strlen($sys_all_mentions_question_marks)-1);
				}
			}

		}

		//GETTING FIRST URL IN NEWS TEXT
		$url = $miscellaneousObject->getUrlFromNewsText($input_news_text);
		if($url != ""){
			$sys_url_info = $miscellaneousObject->getWebsiteHtmlInfo($url);
			if($sys_url_info["title"] != "" && $sys_url_info["img"] != ""){
				$url_image = $sys_url_info["img"];
				$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . NEWS_LINKS_TABLE_NAME . " (news_id, link, link_title, cover_image, video_detected) VALUES (?, ?, ?, ?, ?)" , 5, "sssss", array($sys_news_id, $url, $sys_url_info["title"], $sys_url_info["img"], ""));
			} else {
				$url = "";
				$url_image = "";
			}
		} else {
			$url = "";
			$url_image = "";
		}

		if(count($sys_all_mentions_array) > 0){

			$news_type_real = $newsObject->getNewsType($news_type, $input_news_text, $sys_video_name_for_database, $sys_image_was_received_string, $url, $url_image, "", true, "", "", "", "", "");
					
			$news_fetch_query =  "SELECT "  
			. USER_BIO_TABLE_NAME . ".pot_name, "
			. USER_BIO_TABLE_NAME . ".fcm_token, "
			. USER_BIO_TABLE_NAME . ".fcm_token_ios, "
			. USER_BIO_TABLE_NAME . ".profile_picture FROM "
			. USER_BIO_TABLE_NAME . " INNER JOIN " 
			. LOGIN_TABLE_NAME . " ON  "  
			. USER_BIO_TABLE_NAME . ".investor_id="  
			. LOGIN_TABLE_NAME . ".id "
			. " WHERE " . USER_BIO_TABLE_NAME . ".pot_name in ($sys_all_mentions_question_marks) AND " . LOGIN_TABLE_NAME . ".flag = 0 ORDER BY " .  USER_BIO_TABLE_NAME . ".sku DESC";

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, count($sys_all_mentions_array), $sys_all_mentions_value_type_strings, $sys_all_mentions_array);

			if($prepared_statement !== false){
				$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
					USER_BIO_TABLE_NAME . ".pot_name", 
					USER_BIO_TABLE_NAME . ".fcm_token", 
					USER_BIO_TABLE_NAME . ".fcm_token_ios",
					USER_BIO_TABLE_NAME . ".profile_picture"
				), 4, 2);

				//BINDING THE RESULTS TO VARIABLES
				$prepared_statement_results_array->bind_result($pot_name, $fcm_token, $fcm_token_ios, $profile_picture);

				while($prepared_statement_results_array->fetch()){
					$receiver_keys = array();
					if(trim($fcm_token) != "fcm_token" && trim($fcm_token) != ""){
						$receiver_keys[count($receiver_keys)] = $fcm_token;
					}
					if(trim($fcm_token_ios) != "fcm_token_ios" && trim($fcm_token_ios) != ""){
						$receiver_keys[count($receiver_keys)] = $fcm_token_ios;
					}
					if(count($receiver_keys) > 0){

					//echo "\n\n pot_name : " . $pot_name ." -- fcm_token : " . $fcm_token . " -- fcm_token_ios : " . $fcm_token_ios;

						$miscellaneousObject->sendNotificationToUser(FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, $receiver_keys, $sys_profile_picture, "normal", "general_notification", "mention", $sys_news_id, $input_mypottname, $languagesObject->getLanguageString("tagged_in_news", $input_language), "@" . $input_mypottname . " " . $languagesObject->getLanguageString("mentioned_you_in_a_post", $input_language), date("F j, Y"), $news_type_real);
					}
				}
			}

		}

	}


	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "",  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on
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
