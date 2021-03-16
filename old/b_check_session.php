<?php


if(!isset($_SESSION["b_e_user"])){


      include(ROOT_PATH . 'inc/auto_logout.php');

} else{

	$b_s_e_investor_id = $_SESSION['b_e_user'];
	$b_s_e_investor_id = trim($b_s_e_investor_id);

	include(ROOT_PATH . 'inc/b_get_fold.php');

	$b_e_investor_id = $b_old_fold;
	$b_e_investor_id = trim($b_e_investor_id);
/*
		echo "s_e_investor_id : " . $b_s_e_investor_id;
		echo "<pre>";
		echo "e_investor_id : " . $b_e_investor_id;
		exit;
*/
	if($b_s_e_investor_id != $b_e_investor_id){
      include(ROOT_PATH . 'inc/auto_logout.php');
		
	}


}