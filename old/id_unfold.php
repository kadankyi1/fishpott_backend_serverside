<?php

if(isset($_SESSION["user"]) & isset($_SESSION["e_user"])){
//echo "HERE IN"; exit;

  $user_id = $_SESSION["user"];

  
  include(ROOT_PATH . 'inc/id_fold.php');
  $e_investor_id = $e_user_id;
  $s_e_investor_id = $_SESSION["e_user"];

/*    
    echo "s_e_investor_id : " . $s_e_investor_id;
    echo "<pre>";
    echo "e_investor_id : " . $e_investor_id;
    echo "<pre>";
    echo "user_sys_id : " . $_SESSION["user_sys_id"];
    exit;
*/

  if($s_e_investor_id == $e_investor_id) {


    $investor_id = $_SESSION["user_sys_id"];
    $e_investor_id = $_SESSION["e_user"];

  } else {

      include(ROOT_PATH . 'inc/auto_logout.php');
  }
} else{

      include(ROOT_PATH . 'inc/auto_logout.php');
}
