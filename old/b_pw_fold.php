<?php

$b_secret = 't6k3th3f1rstst3p';


if(isset($b_password)){

	$b_e_password = md5($b_secret . $b_password);

}

if(isset($b_login_type)){

	$b_e_login_type = md5($b_secret . $b_login_type);

}