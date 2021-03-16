<?php


include(ROOT_PATH . 'inc/set_check_login_type.php');

if(!isset($_GET["e_o"])){

	$news = $e_o['8'];
	header("Location: ../user/index.php?fold=$old_fold&e_o=$news&login=$old_e_login&u_type=$old_e_u_type");		

}

if(isset($_GET["e_o"])){

		for ($i=0; $i < 13; $i++) { 


			if($_GET["e_o"] == $e_o[$i]) {

				$x1 = 1;
			}

		}

	if($x1 != 1) {
	$news = $e_o['8'];
	header("Location: ../user/index.php?fold=$old_fold&e_o=$news&login=$old_e_login&u_type=$old_e_u_type");		
	}
}
