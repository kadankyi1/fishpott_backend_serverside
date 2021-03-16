<?php


if($b_old_e_login == "70f40e0d49aa0121f92d8c7b4b12e403"){

	$b_login_type = "phone";

} elseif($b_old_e_login == "a7c30f63caeeae0323925c41b3726c96") {

	$b_login_type = "email";
} else {

	include(ROOT_PATH . 'inc/auto_logout.php');
}



if($b_old_e_u_type == "6172ad9da8095756b63d7c58077f286f"){

	$b_inputtor_type = "investor";
	$_SESSION['b_inputtor_type'] = $b_inputtor_type;

}


if($b_old_e_u_type == "541638e6cb0bb1a0df0a8eb73d2f2135"){

	$b_inputtor_type = "business";
	$_SESSION['b_inputtor_type'] = $b_inputtor_type;

}


