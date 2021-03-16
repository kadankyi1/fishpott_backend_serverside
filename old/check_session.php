<?php

if(!isset($_SESSION["e_user"])){
//echo "HERE OUT"; exit;
      include(ROOT_PATH . 'inc/auto_logout.php');

} else{
//echo "HERE IN"; exit;
	$s_e_investor_id = $_SESSION['e_user'];
	$s_e_investor_id = trim($s_e_investor_id);

	include(ROOT_PATH . 'inc/get_fold.php');

	$e_investor_id = $old_fold;
	$e_investor_id = trim($e_investor_id);
	
	if($s_e_investor_id != $e_investor_id){
      include(ROOT_PATH . 'inc/auto_logout.php');
		
	}


}
