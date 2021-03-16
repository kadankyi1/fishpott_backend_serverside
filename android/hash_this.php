<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if(	isset($_GET["kw"]) && trim($_GET["kw"]) != "" &&
	isset($_GET["typ"]) && trim($_GET["typ"]) != "") {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	//CALLING THE INPUT VALIDATOR CLASS
	include_once 'classes/input_validation_class.php';
	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	$input_keyword = trim($_GET["kw"]);

	$input_keyword_hashed = $validatorObject->hashString($input_keyword);

	echo "input_keyword_hashed : " . $input_keyword_hashed; exit;



}