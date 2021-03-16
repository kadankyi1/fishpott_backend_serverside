<?php
if(isset($_GET["u_type"]) && $_GET["u_type"] == "541638e6cb0bb1a0df0a8eb73d2f2135" && !isset($_SESSION["b_straight"])) {

	if(isset($_SESSION["e_user"]) && $_SESSION["e_user"] != "" && $_SESSION['enter_type'] == "investor"){

		$_SESSION["investor_e_user"] = $_SESSION["e_user"];
		$_SESSION["investor_user"] = $_SESSION["user"];
		$_SESSION["investor_login_type"] = $_SESSION["login_type"];
		$_SESSION["investor_user_sys_id"] = $_SESSION["user_sys_id"];
		$_SESSION["investor_user_type"] = $_SESSION["user_type"];
	    $i_old_fold = $_SESSION["e_user"];
	    $i_old_login = $_SESSION["login_type"];
	    $i_old_u_type = $_SESSION["user_type"];
	    $i_sys_id = $_SESSION["investor_user_sys_id"];

	} else {

	    $i_old_fold = $_SESSION["investor_e_user"];
	    $i_old_login = $_SESSION["investor_login_type"];
	    $i_old_u_type = $_SESSION["investor_user_type"];
	    $i_sys_id = $_SESSION["investor_user_sys_id"];
	}
		    include(ROOT_PATH . 'inc/db_connect.php');
	$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$i_sys_id'"; 

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
		$real_p_i_profile_picture = $row["profile_picture"];
		$p_acc_full_name = $row["first_name"] . " " . $row["last_name"];

    }
//echo "SESSION e_user : " . $_SESSION["e_user"] . "<br>";
//echo "SESSION user : " . $_SESSION["user"] . "<br>";
//echo "SESSION login_type : " . $_SESSION["login_type"] . "<br>";
//echo "SESSION user_sys_id : " . $_SESSION["user_sys_id"] . "<br>";
//echo "SESSION user_type : " . $_SESSION["user_type"] . "<br><br><br><br>";


	if(isset($_GET["bp"])) {
		    include(ROOT_PATH . 'inc/db_connect.php');
			$bness_id = $_GET["bp"];
			$query = "SELECT number_login, email_login, login_type FROM wuramu WHERE id = '$bness_id'"; 

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != 0) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
		        $user_type = $row["login_type"];
		        if($row["number_login"] != ""){

		            $user_id = $row["number_login"];
		            $login_type = "phone";

		        } else {

		            $user_id = $row["email_login"];
		            $login_type = "email";

		        }
		        include(ROOT_PATH . 'inc/id_fold.php');
		        include(ROOT_PATH . 'inc/pw_fold.php');

		        $_SESSION["b_e_user"] = $e_user_id;
		        $_SESSION["b_user"] = $user_id;
		        $_SESSION["b_login_type"] = $e_login_type;
		        $_SESSION["b_user_sys_id"] = $bness_id;
				$_SESSION["b_user_type"] = $e_user_type;

		} else {

      			include(ROOT_PATH . 'inc/auto_logout.php');
		}
	}

		$_SESSION["e_user"] = $_SESSION["b_e_user"];
		$_SESSION["user"] = $_SESSION["b_user"];
		$_SESSION["login_type"] = $_SESSION["b_login_type"];
		$_SESSION["user_sys_id"] = $_SESSION["b_user_sys_id"];
		$_SESSION["user_type"] = $_SESSION["b_user_type"];

} else {

	if(isset($_SESSION["investor_e_user"]) && $_SESSION["investor_e_user"] != ""){

		$_SESSION["e_user"] = $_SESSION["investor_e_user"];
		$_SESSION["user"] = $_SESSION["investor_user"];
		$_SESSION["login_type"] = $_SESSION["investor_login_type"];
		$_SESSION["user_sys_id"] = $_SESSION["investor_user_sys_id"];
		$_SESSION["user_type"] = $_SESSION["investor_user_type"];
	}

}

//echo "SESSION b_e_user : " . $_SESSION["b_e_user"] . "<br>";
//echo "SESSION b_user : " . $_SESSION["b_user"] . "<br>";
//echo "SESSION b_login_type : " . $_SESSION["b_login_type"] . "<br>";
//echo "SESSION b_user_sys_id : " . $_SESSION["b_user_sys_id"] . "<br>";
//echo "SESSION e_user_type : " . $_SESSION["e_user_type"] . "<br><br><br><br>";


//echo "SESSION e_user : " . $_SESSION["e_user"] . "<br>";
//echo "SESSION user : " . $_SESSION["user"] . "<br>";
//echo "SESSION login_type : " . $_SESSION["login_type"] . "<br>";
//echo "SESSION user_sys_id : " . $_SESSION["user_sys_id"] . "<br>";
//echo "SESSION user_type : " . $_SESSION["user_type"] . "<br>"; exit;
