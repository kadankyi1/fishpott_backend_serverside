<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["session_id"]) &&
	isset($_POST["log_id_token"]) && trim($_POST["log_id_token"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["all_contacts_names"]) && 
	isset($_POST["all_contacts_numbers"]) && 
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE SESSION CLASS FILE
	include_once 'classes/session_class.php';
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
	//CALLING TO THE COUNTRY CODES CLASS
	include_once 'classes/country_codes_class.php';
	//CALLING TO THE SUPPORTED FILE CLASS
	include_once 'classes/file_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_id = trim($_POST["log_id_token"]);
	$input_pass = trim($_POST["log_pass_token"]);
	$input_mypottname = trim($_POST["mypottname"]);
	$session_id = trim($_POST["session_id"]);
	$input_language = trim($_POST["language"]);

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS
	$government_id_verification_is_on = false;

	// A TOGGLE TO KNOW IF THE CONTACTS ARE SAVED TO THE SESSION OR NOT
	$sys_contacts_saved_to_session = false;
	$sysResponse["linkups_suggestions_returned"] = array();


	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$fileObject = new fileActions();
	
	// CREATING COUNTRY CODES OBJECT
	$countryCodesObject = new countryCodes();

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	// CREATING THE SESSION ACTIONS OBJECT
	$sessionObject = new sessionsActions();

	// STARTING A SESSION FOR THE USER
	if(!isset($session_id) || trim($session_id) == ""){
		$session_id = $miscellaneousObject->getRandomString(40);
	}

	// THE MAXIMUM TIME THE SESSION IS ALLOWED TO BE VALID
	$max_allowed_session_time = 1500;

	$sessionObject->startSession($session_id, $max_allowed_session_time);

	// CHECKING IF THE SESSION
	if($sessionObject->sessionIsNoLongerValid($max_allowed_session_time) === true){
		//$sessionObject->destroySession();
		$session_id = $miscellaneousObject->getRandomString(40);
		$sessionObject->startSession($session_id, $max_allowed_session_time);
	}

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if(	
		$validatorObject->stringContainsNoTags($input_id) !== true
	){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, government_id_verified, request_government_id FROM " . LOGIN_TABLE_NAME . " WHERE id = ?", 1, "s", array($input_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "government_id_verified", "request_government_id"), 5, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "password" || trim($prepared_statement_results_array[1]) == "number_verified"){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){
		$miscellaneousObject->respondFrontEnd3(0, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	}

	//CHECKING IF THE INPUT PASSWORD MATCHES THE DATABASE PASSWORD OTHERWISE WE FAIL THE REQUEST
	if($prepared_statement_results_array[0] != $input_pass){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("session_closed_restart_the_app_and_login_to_start_a_new_session", $input_language));
	}

	//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
	if($prepared_statement_results_array[3] == 0 && $prepared_statement_results_array[4] == 1){
		$government_id_verification_is_on = true;
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
			$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}

	} else {
		$phone_verification_is_on = false;
	}

	//UPDATING THE LAST SEENN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));


	//DECLARING THE ARRAYS AND STRINGS FOR THE QUERY
	$query_values_holder = array();
	$last_five_letters_values_holder = array();
	$input_all_contacts_numbers_array = array();
	$input_all_contacts_names_array = array();
	$query_values_regex = "";
	$query_valuestype_string = "";
	$query_questionmarks_string = " phone IN( ";
	$query_regex_string = " phone REGEXP(?)";
	$query_questionmarks_fetchedpotts_string = "";
	$last_phone = "";
	$first_phone = "";

	if(!isset($_SESSION["sent_potts"])){
		$_SESSION["sent_potts"] = array();
	}

	// TAKING THE PHONE NUMBERS AND PROCESSING THEM
	if(!isset($_SESSION["processed_all_contacts_numbers"]) ){
		if(isset($_SESSION["all_contacts_numbers"])){
			if(trim($_SESSION["all_contacts_numbers"]) != ""){
				$_SESSION["all_contacts_numbers"] = $_SESSION["all_contacts_numbers"];
				$_SESSION["all_contacts_names"] = $_SESSION["all_contacts_names"];
				// GETTING ALL THE CONTACTS NAMES AND NUMBERS INTO AN ARRAY
				$input_all_contacts_names_string = trim($_SESSION["all_contacts_names"]);
				$input_all_contacts_names_array = explode(" | ", $input_all_contacts_names_string);
				$input_all_contacts_numbers_string = trim($_SESSION["all_contacts_numbers"]);
				$input_all_contacts_numbers_array = explode(" | ", $input_all_contacts_numbers_string);
				$last_phone = $input_all_contacts_numbers_array[count($input_all_contacts_numbers_array) - 1];
				$first_phone = $input_all_contacts_numbers_array[0];

			}
		} else if (trim($_POST["all_contacts_numbers"]) != "") {
			$_SESSION["all_contacts_numbers"] = $_POST["all_contacts_numbers"];
			$_SESSION["all_contacts_names"] = $_POST["all_contacts_names"];
			// GETTING ALL THE CONTACTS NAMES AND NUMBERS INTO AN ARRAY
			$input_all_contacts_names_string = trim($_POST["all_contacts_names"]);
			$input_all_contacts_names_array = explode(" | ", $input_all_contacts_names_string);
			$input_all_contacts_numbers_string = trim($_POST["all_contacts_numbers"]);
			$input_all_contacts_numbers_array = explode(" | ", $input_all_contacts_numbers_string);
			$last_phone = $input_all_contacts_numbers_array[count($input_all_contacts_numbers_array) - 1];
			$first_phone = $input_all_contacts_numbers_array[0];
		}
	} else {
		$input_all_contacts_names_array = $_SESSION["processed_all_contacts_names"];
		$input_all_contacts_numbers_array = $_SESSION["processed_all_contacts_numbers"];
		$last_phone = $input_all_contacts_numbers_array[count($input_all_contacts_numbers_array) - 1];
		$first_phone = $input_all_contacts_numbers_array[0];
	}

	// THESE VARIABLES ARE USED FOR SAVING FOR DATABSE
	$input_all_contacts_names_array_for_db_save = $input_all_contacts_names_array;
	$input_all_contacts_numbers_array_for_db_save = $input_all_contacts_numbers_array;



	if(isset($_SESSION["all_contacts_numbers"]) && trim($_SESSION["all_contacts_numbers"]) != "" && count($input_all_contacts_numbers_array) > 1) {
		$sys_contacts_saved_to_session = true;

		$_SESSION["processed_all_contacts_numbers"] = array();
		//MAKING SURE THEY ARE PHONE NUMBERS TO WORK WITH
		// CHECKING IF THE NUMBER BEGINS WITH A "+" OR NOT AND PREPARING IT FOR THE QUERY
		for ($i=0; $i < count($input_all_contacts_numbers_array); $i++) { 
			$current_number = $input_all_contacts_numbers_array[$i];
			$current_number = $validatorObject->removeAllCharactersAndLeaveNumbers($current_number);
            array_push($_SESSION["processed_all_contacts_numbers"], $current_number);    
			if($current_number === false){		
				continue;
			}
			$last_5_char = substr($current_number,strlen($current_number)-5,strlen($current_number));
			$last_five_letters_values_holder[$last_5_char] = $current_number;
			if(substr($current_number,0,1) != "+"){
				$next = substr($current_number,1,strlen($current_number));
				if(strlen($current_number) < 9 || strlen($current_number) > 15){

					continue;
				}
            	$query_values_regex = $query_values_regex . $next . "|" ;
			} else {
				$next = $current_number;
				if(strlen($current_number) < 9 || strlen($current_number) > 15){
					continue;
				}
            	$query_questionmarks_string = $query_questionmarks_string . "?,";
            	array_push($query_values_holder, $next);    
            	$query_valuestype_string = $query_valuestype_string . "s";
			}
		}

		$query_questionmarks_string = substr($query_questionmarks_string, 0, -1);
		$query_values_regex = substr($query_values_regex, 0, -1);
		$query_questionmarks_string = $query_questionmarks_string . ") ";

        if(trim($query_valuestype_string) == ""){
        	$query_questionmarks_string = "";
        	$or = "";
        } else {
        	$or = " OR ";
        }

        if($query_values_regex == ""){
        	$query_regex_string = "";
        	$or = "";
        } else {
	        array_push($query_values_holder, $query_values_regex);    
	        $query_valuestype_string = $query_valuestype_string . "s";
        }

        if(trim($query_valuestype_string) != ""){
			$contacts_query = 'SELECT first_name, last_name, pot_name, phone, profile_picture, verified_tag, investor_id FROM  ' . USER_BIO_TABLE_NAME . ' WHERE ' . $query_questionmarks_string . $or . $query_regex_string;

			//FETCHING POTTS
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $contacts_query, count($query_values_holder), $query_valuestype_string, $query_values_holder);

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
			}

			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("first_name", "last_name", "pot_name", "phone", "profile_picture", "verified_tag", "investor_id"), 7, 2);
			//BINDING THE RESULTS TO VARIABLES
			$prepared_statement_results_array->bind_result($first_name, $last_name, $pot_name, $phone, $profile_picture, $verified_tag, $investor_id);

			$count = 0;

			// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
		    while($prepared_statement_results_array->fetch()){
		        $fullname = $first_name." ".$last_name;
				if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
					$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
				} else {
					$profile_picture = "";
				}
		        if($count == 15){
		        	break;
		        }
				$last_5_char = substr($phone,strlen($phone)-5,strlen($phone));

				if(isset($last_five_letters_values_holder[$last_5_char])){
					$_SESSION["processed_all_contacts_numbers"] = array_diff($_SESSION["processed_all_contacts_numbers"], array($last_five_letters_values_holder[$last_5_char]));
				}

				if($input_mypottname == $pot_name){
					continue;
				}

		        //PREVENTING POTTS FROM REPEATING
				if(isset($_SESSION["sent_potts"][$pot_name])){
					continue;
				}

		        // GETTING THE KIND OF ACCOUNT
				$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT login_type FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($phone));
				if($prepared_statement_2 === false){
					continue;
				}
				$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("login_type"), 1, 1);

				if($prepared_statement_results_array_2 === false){
					continue;
				}
				if(trim($prepared_statement_results_array_2[0]) == "login_type"){
					continue;
				}
				if($prepared_statement_results_array_2[0] == "investor"){
					$account_type = 1;
				} else if($prepared_statement_results_array_2[0] == "business"){
					$account_type = 2;
				} else {
					continue;
				}

		        // CHECKING IF YOU ARE ALREADY LINKED TO AN ACCOUNT
				$prepared_statement_3 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT status FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$investor_id));
				if($prepared_statement_3 === false){
					continue;
				}
				$prepared_statement_results_array_3 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_3, array("status"), 1, 1);

				if($prepared_statement_results_array_3 === false){
					continue;
				}

				if($prepared_statement_results_array_3[0] == 1){
					continue;
				}

				$fetch_reason = $languagesObject->getLanguageString("linked_from_your_contact_list", $input_language);

				// SAVING THE FETCHED POTT
				$_SESSION["sent_potts"][$pot_name] = $verified_tag;
				$next  = array(
					'1' => $pot_name, 
					'2' => $fullname, 
					'3' => $profile_picture,  
					'4' => $verified_tag, 
					'5' => $investor_id, 
					'6' => $account_type, 
					'7' => $fetch_reason
					);
		        array_push($sysResponse["linkups_suggestions_returned"], $next); 
		        $count++;

		    } // end of while

        }

	}

// FETCHING MORE LINKUPS IF WE GOT LESS THAN 15 FROM CONTACTS
if($count < 15) {


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT  first_name, last_name, pot_name, phone, profile_picture, verified_tag, investor_id, net_worth, investing_points, total_amount_made_onfp FROM  " . USER_BIO_TABLE_NAME . " ORDER BY RAND() LIMIT 50", 0, "", "");

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("first_name", "last_name", "pot_name", "phone", "profile_picture", "verified_tag", "investor_id", "net_worth", "investing_points", "total_amount_made_onfp"), 10, 2);
	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($first_name, $last_name, $pot_name, $phone, $profile_picture, $verified_tag, $investor_id, $net_worth, $investing_points, $total_amount_made_onfp);

	// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
    while($prepared_statement_results_array->fetch()){
    	if(!isset($_SESSION[$pot_name])){
	        $fullname = $first_name." ".$last_name;
	        $fetch_reason = "";
			if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
				$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
			} else {
				$profile_picture = "";
			}
	        if($count == 15){
	        	break;
	        }

	        //PREVENTING POTTS FROM REPEATING
			if(isset($_SESSION["sent_potts"][$pot_name])){
				continue;
			}

	        // GETTING THE KIND OF ACCOUNT
			$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT login_type FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($phone));
			if($prepared_statement_2 === false){
				continue;
			}
			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("login_type"), 1, 1);

			if($prepared_statement_results_array_2 === false){
				continue;
			}
			// IF THE DATABASE QUERY GOT NO RESULTS
			if(trim($prepared_statement_results_array_2[0]) == "login_type"){
				continue;
			}
			// CHECKING IF YOUR ACCOUNT IS BUSINESS OR PERSONAL
			if($prepared_statement_results_array_2[0] == "investor"){
				$account_type = 1;
			} else if($prepared_statement_results_array_2[0] == "business"){
				$account_type = 2;
			} else {
				continue;
			}
			if($input_mypottname == $pot_name){
				continue;
			}

	        // CHECKING IF YOU ARE ALREADY LINKED TO AN ACCOUNT
			$prepared_statement_3 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT status FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$investor_id));
			if($prepared_statement_3 === false){
				continue;
			}
			$prepared_statement_results_array_3 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_3, array("status"), 1, 1);

			if($prepared_statement_results_array_3 === false){
				continue;
			}

			if($prepared_statement_results_array_3[0] == 1){
				continue;
			}

	        $reason_type = rand(1,3);

	        if($investing_points > SWIFT_INVESTOR_UPPER_LIMIT && $investing_points <= DEMI_GOD_INVESTOR_UPPER_LIMIT){
	        	$fetch_reason = $languagesObject->getLanguageString("a_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
		    } else if($investing_points > DEMI_GOD_INVESTOR_UPPER_LIMIT && $investing_points <= GOD_INVESTOR_UPPER_LIMIT){
	        	$fetch_reason = $languagesObject->getLanguageString("a_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
		    } else if($net_worth > TOTAL_AMOUNT_OF_PEARLS_ON_FP_CONSIDERED_A_LOT){
	        	$fetch_reason = $languagesObject->getLanguageString("makes_a_lot_of_pearls", $input_language);
		    } 

	        // CHECKING FOR A REASON FOR SUGGESTION
	        if($reason_type == 1 && $fetch_reason == ""){ // USING THE INVESTOR POINTS
		        if($investing_points > BABY_INVESTOR_UPPER_LIMIT && $investing_points <= TODDLER_INVESTOR_UPPER_LIMIT){
		        	$fetch_reason = $languagesObject->getLanguageString("a_toddler_investor", $input_language);
		        } else if($investing_points > TODDLER_INVESTOR_UPPER_LIMIT && $investing_points <= SWIFT_INVESTOR_UPPER_LIMIT){
		        	$fetch_reason = $languagesObject->getLanguageString("a_swift_investor", $input_language);
		        } else if($investing_points > SWIFT_INVESTOR_UPPER_LIMIT && $investing_points <= DEMI_GOD_INVESTOR_UPPER_LIMIT){
		        	$fetch_reason = $languagesObject->getLanguageString("a_demi_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
		        } else if($investing_points > DEMI_GOD_INVESTOR_UPPER_LIMIT && $investing_points <= GOD_INVESTOR_UPPER_LIMIT || $investing_points > GOD_INVESTOR_UPPER_LIMIT  ){
		        	$fetch_reason = $languagesObject->getLanguageString("a_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
		        }
	        } else if ($reason_type == 2 && $fetch_reason == ""){ // USING THE TOTAL AMOUNT MADE ON FISHPOTT
		        if($total_amount_made_onfp > TOTAL_AMOUNT_MADE_ON_FP_CONSIDERED_A_LOT){
		        	$fetch_reason = $languagesObject->getLanguageString("makes_a_lot_of_income_on_from_their_fishpott", $input_language);
		        } 
	        } else if ($reason_type == 3 && $fetch_reason == ""){ // USING THE TOTAL PEARLS
		        if($net_worth > TOTAL_AMOUNT_OF_PEARLS_ON_FP_CONSIDERED_A_LOT){
	        		$fetch_reason = $languagesObject->getLanguageString("makes_a_lot_of_pearls", $input_language);
		        } 
	        }

			$_SESSION["sent_potts"][$pot_name] = $verified_tag;
			$next  = array(
				'1' => $pot_name, 
				'2' => $fullname, 
				'3' => $profile_picture,  
				'4' => $verified_tag, 
				'5' => $investor_id, 
				'6' => $account_type,
				'7' => $fetch_reason
				);
	        array_push($sysResponse["linkups_suggestions_returned"], $next); 
	        $count++;
    	}
    } // end of while


}

	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "1", 
		'3' => $sys_contacts_saved_to_session,  
		'4' => $session_id, 
		'5' => $phone_verification_is_on, 
		'6' => CURRENT_HIGHEST_VERSION_CODE,
		'7' => FORCE_UPDATE_STATUS,
		'8' => UPDATE_DATE,
		'9' => $government_id_verification_is_on

		);
	echo json_encode($sysResponse);

/**********************************************************************************************************************
				
				SAVE USERS PHONE CONTACTS HERE

**********************************************************************************************************************/

// GETTING THE COUNTRY CODE
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT country FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_id));

if($prepared_statement === false){
	$sys_country_code = "";
}

// GETTING RESULTS OF QUERY INTO AN ARRAY
$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("country"), 1, 1);

if($prepared_statement_results_array === false){
	$sys_country_code = "";
} else {
	$sys_country = $prepared_statement_results_array[0];
	$sys_country_code = $countryCodesObject->getCountryCodeString($sys_country);
}

for ($i=0; $i < count($input_all_contacts_numbers_array_for_db_save); $i++) { 
	if(trim($input_all_contacts_numbers_array_for_db_save[$i]) != ""){

		$this_raw_number = trim($input_all_contacts_numbers_array_for_db_save[$i]);
		$this_number = $validatorObject->removeAllCharactersAndLeaveNumbers($input_all_contacts_numbers_array_for_db_save[$i]);
		if(substr($this_raw_number,0,1) != "+"){
			$this_number = $sys_country_code . substr($this_number,1,strlen($this_number));
		} else {
			$this_number =  "+" . $this_number;
		}
		$sys_contact_id = $input_id . "_" . substr($this_number,1,strlen($this_number));
		
		//INSERTING THE NEW USER DATA INTO THE USER BIO TABLE DATABASE
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . PHONE_CONTACTS_TABLE_NAME . " (contact_id,owner_id,this_number,recognized_name) VALUES (?,?,?,?)" , 4, "ssss", array($sys_contact_id, $input_id, $this_number, $input_all_contacts_names_array_for_db_save[$i]));
	}
}


// CLOSE DATABASE CONNECTION
if($prepared_statement !== false){
	$dbObject->closeDatabaseConnection($prepared_statement);
}
 exit;
}

?>