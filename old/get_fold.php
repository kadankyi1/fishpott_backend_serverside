<?php

if(isset($_GET["fold"])){

	$old_fold = $_GET["fold"];

} else {

      include(ROOT_PATH . 'inc/auto_logout.php');
}

if(isset($_GET["e"])){

	$e_error = $_GET["e"];

} 

if(isset($_GET["login"])){

	$old_e_login= $_GET["login"];

} else {

      include(ROOT_PATH . 'inc/auto_logout.php');
}
if(isset($_GET["u_type"])){

	$old_e_u_type= $_GET["u_type"];
} else {

      include(ROOT_PATH . 'inc/auto_logout.php');
}

