<?php

session_start();
require_once("config.php");
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');

//Fetching Values from URL  
$i_o_password2=$_POST['i_o_password1'];


$i_n_password2=$_POST['i_n_password1'];


$table_name = "investor";
$column1_name = "password";
$password = $i_n_password2;

include(ROOT_PATH . 'inc/pw_fold.php');

$column1_value = $e_password;
$row_check = "password";
$password = $i_o_password2;

include(ROOT_PATH . 'inc/pw_fold.php');

$row_check_value = $e_password;

include(ROOT_PATH . 'inc/update1_query.php');

  if($done == 1){
  echo "Data Submitted succesfully";
  }
   
?>
