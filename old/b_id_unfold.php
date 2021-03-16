<?php

if(isset($_SESSION["b_user"]) & isset($_SESSION["b_e_user"])){

  $b_user_id = $_SESSION["b_user"];

  
  include(ROOT_PATH . 'inc/b_id_fold.php');
  $b_e_investor_id = $b_e_user_id;
  $b_s_e_investor_id = $_SESSION["b_e_user"];


/*    
    echo "b_s_e_investor_id : " . $b_s_e_investor_id;
    echo "<pre>";
    echo "b_e_investor_id : " . $b_e_investor_id;
    echo "<pre>";
    echo "b_user_sys_id : " . $_SESSION["b_user_sys_id"];
    exit;
*/

  if($b_s_e_investor_id == $b_e_investor_id) {


    $b_investor_id = $_SESSION["b_user_sys_id"];
    $b_e_investor_id = $_SESSION["b_e_user"];

  } else {

      include(ROOT_PATH . 'inc/auto_logout.php');
  }
} else{

      include(ROOT_PATH . 'inc/auto_logout.php');
}
