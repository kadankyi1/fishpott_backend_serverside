<?php

if($old_e_login == "70f40e0d49aa0121f92d8c7b4b12e403"){

	$login_type = "phone";

} elseif($old_e_login == "a7c30f63caeeae0323925c41b3726c96") {

	$login_type = "email";
} else {

	include(ROOT_PATH . 'inc/auto_logout.php');
}

if($old_e_u_type == "6172ad9da8095756b63d7c58077f286f"){

	$inputtor_type = "investor";
	$enter_type = "investor";
	$_SESSION['inputtor_type'] = $inputtor_type;
	$_SESSION['enter_type'] = $enter_type;

} elseif($old_e_u_type == "541638e6cb0bb1a0df0a8eb73d2f2135") {

	$inputtor_type = "investor";
	$enter_type = "business";
	$_SESSION['inputtor_type'] = $inputtor_type;
	$_SESSION['enter_type'] = $enter_type;
}


