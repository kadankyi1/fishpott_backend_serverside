<?php

class inputValidator {

 
		public function stringContainsNoTags($input) {

			// CHECK THAT IT IS A REAL STRING
			if($input != ""){
				/*
				if(filter_var($input, FILTER_SANITIZE_STRING)){
					$validation = true;
				} else {
					$validation = false;
				}
				*/
				if($input != strip_tags($input)) {
					$validation = false;
				} else {
					$validation = true;
				}
				return $validation;

			} else {
				return false;
			}

		} // END OF validateInputString

		public function stringIsNotMoreThanMaxLength($input, $max_allowed_input_length){

			//CHECK THAT THE INPUT STRING IS NOT MORE THAN THE MAXIMUM ALLOWED LENGTH
			if($input != "" && $max_allowed_input_length > 0){
				if(strlen($input) > $max_allowed_input_length){
					$validation = false;
				} else {
					if($validation != false){
						$validation = true;
					}
				}
				return $validation;
			} else {
				return false;
			}

		}

		public function inputContainsOnlyNumbers($input){
			if(trim($input) == ""){
				return false;
			}
			if (ctype_digit($input)) {
				return true;
		    } else {
				return false;
		    }

		}

		public function inputContainsOnlyAlphabetsWithUnderscore($input, $include_some_special_characters, $special_characters_array){
			if(trim($input) == ""){
				return false;
			}

			if($include_some_special_characters === true){
				for ($i=0; $i < count($special_characters_array); $i++) {

					$input = str_replace($special_characters_array[$i],"",$input);

				}
			}

			if (!preg_match('/[^A-Za-z0-9]/', $input)) {
				return true;
			} else {
				return false;
			}

		}

		public function hashString($input){
			if($input != ""){
				$secret = "t6k3th3f1rstst3p";
				$hashed_input = md5($secret . $input);
				return $hashed_input;
			} else {
				return false;				
			}

		} // END OF hashString

		public function fileExists($file_location){
			if($file_location != ""){
				if (file_exists($file_location)) {
					return $file_location; 
				} else {
					return false;				
				}
			} else {
				return false;				
			}

		} // END OF fileExists

		public function removeAllCharactersAndLeaveNumbers($input){
			if($input != ""){
				return preg_replace('/[^0-9]/', '', $input);
			} else {
				return false;				
			}

		}


}	