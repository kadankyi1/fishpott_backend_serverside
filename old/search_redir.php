<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');

//echo "_SERVER[REQUEST_METHOD] : " . $_SERVER["REQUEST_METHOD"] . "<br>";
//echo "_SESSION[e_user] : " . $_SESSION["e_user"] . "<br>";
//var_dump($_POST);
///echo "my_search : " . $_POST["my_search"] . "<br>"; 
//exit;


if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["my_search"] != "" && $_SESSION["e_user"] != "") {

	$my_search = $_POST["my_search"];
	$my_search = mysqli_real_escape_string($mysqli, $my_search);

	$_SESSION["my_search"] = $my_search;
    $fold = $_SESSION["e_user"];
    $login = $_SESSION["login_type"];
    $u_type = $_SESSION["user_type"];
	header("Location: ../user/index.php?fold=$fold&e_o=5dfef3b0cb2092e8142d3b39600b403d&login=$login&u_type=$u_type");		
} else {
	//echo "ABOUT TO LOGOUT"; exit;
	include(ROOT_PATH . 'inc/auto_logout.php');

}

/*
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect.php'); 

$keyword = $_SESSION["keyword"];

$cnt = 0;

$table_name = "students"; 
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {


		require(ROOT_PATH . 'inc/error.php');
}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

		$query = "SELECT * FROM students WHERE sku = $i AND first_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $id = $row["id"];
		  $first_name = $row["first_name"];
		  $middle_name = $row["middle_name"];
		  $last_name = $row["last_name"];
		  $sex = $row["sex"];
		  $dob = $row["dob"];
		  $house_address = $row["house_address"];
		  $class = $row["class"];
		  $parent_full_name = $row["parent_full_name"];
		  $parent_email = $row["parent_email"];
		  $parent_phone_number = $row["parent_phone_number"];
		    $skip = 0;
		    $finds[$finds_cnt]= array($id, $first_name, $middle_name, $last_name, $sex, $dob, $house_address, $class, $parent_full_name, $parent_email, $parent_phone_number);
		    $finds_cnt = $finds_cnt + 1;
		} else {

		    $skip = 1;

		}

		$query = "SELECT * FROM students WHERE sku = $i AND last_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $id = $row["id"];
		  $first_name = $row["first_name"];
		  $middle_name = $row["middle_name"];
		  $last_name = $row["last_name"];
		  $sex = $row["sex"];
		  $dob = $row["dob"];
		  $house_address = $row["house_address"];
		  $class = $row["class"];
		  $parent_full_name = $row["parent_full_name"];
		  $parent_email = $row["parent_email"];
		  $parent_phone_number = $row["parent_phone_number"];
		    $finds[$finds_cnt]= array($id, $first_name, $middle_name, $last_name, $sex, $dob, $house_address, $class, $parent_full_name, $parent_email, $parent_phone_number);
		    $finds_cnt = $finds_cnt + 1;
		    $skip = 0;

		} else {

		    $skip = 1;

		}

		$query = "SELECT * FROM students WHERE sku = $i AND middle_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $id = $row["id"];
		  $first_name = $row["first_name"];
		  $middle_name = $row["middle_name"];
		  $last_name = $row["last_name"];
		  $sex = $row["sex"];
		  $dob = $row["dob"];
		  $house_address = $row["house_address"];
		  $class = $row["class"];
		  $parent_full_name = $row["parent_full_name"];
		  $parent_email = $row["parent_email"];
		  $parent_phone_number = $row["parent_phone_number"];
		    $finds[$finds_cnt]= array($id, $first_name, $middle_name, $last_name, $sex, $dob, $house_address, $class, $parent_full_name, $parent_email, $parent_phone_number);
		    $finds_cnt = $finds_cnt + 1;
		    $skip = 0;

		} else {

		    $skip = 1;

		}

		$query = "SELECT * FROM students WHERE sku = $i AND parent_phone_number LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $id = $row["id"];
		  $first_name = $row["first_name"];
		  $middle_name = $row["middle_name"];
		  $last_name = $row["last_name"];
		  $sex = $row["sex"];
		  $dob = $row["dob"];
		  $house_address = $row["house_address"];
		  $class = $row["class"];
		  $parent_full_name = $row["parent_full_name"];
		  $parent_email = $row["parent_email"];
		  $parent_phone_number = $row["parent_phone_number"];
		    $finds[$finds_cnt]= array($id, $first_name, $middle_name, $last_name, $sex, $dob, $house_address, $class, $parent_full_name, $parent_email, $parent_phone_number);
		    $finds_cnt = $finds_cnt + 1;
		    $skip = 0;

		} else {

		    $skip = 1;

		}


}
*/