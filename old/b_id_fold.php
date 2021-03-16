<?php

$b_secret = 'g0dh6v36llth3p0w3r';


if(isset($b_user_id)){

  $b_e_user_id = md5($b_secret . $b_user_id);


} else{

	include(ROOT_PATH . 'inc/auto_logout.php');
}

if(isset($b_user_type)){

  $b_e_user_type = md5($b_secret . $b_user_type);


} 
