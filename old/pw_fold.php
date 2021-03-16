<?php

$secret = 't6k3th3f1rstst3p';


if(isset($password)){

	$e_password = md5($secret . $password);

}

if(isset($login_type)){

	$e_login_type = md5($secret . $login_type);

}