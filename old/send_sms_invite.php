<?php
    session_start();

    require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user"]) && $_SESSION["user"] != "" && isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "" && $_POST["check_human"] == "") {


		//echo "5" . "<br>";
		$submitedMessage = filter_input(INPUT_POST, "message", FILTER_SANITIZE_STRING);
		$submitedNumbers = filter_input(INPUT_POST, "number", FILTER_SANITIZE_STRING);

		//echo "submitedMessage : " . $submitedMessage ."\n<br>";
		//echo "submitedNumbers : " . $submitedNumbers ."\n<br>"; exit;
		//$submitedNumbers = substr($submitedNumbers,1);
		//$submitedNumbers = "+233" . $submitedNumbers;

		//echo $submitedNumbers; exit;

		//$submitedNumbers = "+233207393447";
		//$submitedMessage = "Please Click The Link Below To View Your Ward's Term's Academic Report \n<br>  " .  "https://ghnow.000webhostapp.com/sms_academic_report.php?urq=12358d4107eb96964.96551088&rtw=1&ary=2016%20/%202017";



		$subject= rawurlencode("subject");
		$message= rawurlencode($submitedMessage);
		$mobileNumbers = rawurlencode($submitedNumbers);
		$senderName = rawurlencode("De Jays Sch"); 
		$username = "fishpottcompany@gmail.com";
		$password = "0ef127be9f50f487adfa804128147a4f2358c263";
		$apiurl ="http://go.mytxtbuddy.com/api/rest/v1_2/sendMessage/".$subject."/".$message."/".$mobileNumbers."/".$senderName."/".$username."/".$password;
		//
		$mtbCurl = curl_init($apiurl);
		//				
		curl_setopt($mtbCurl,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($mtbCurl,CURLOPT_POST,true);
		//
		//
		$response = curl_exec($mtbCurl);
		$response = substr($response,0, 2);

		//echo $response; exit;
		if($response == "OK") {

		echo 1; exit;


		} else {

		echo 0; exit;

		}





}
?>

