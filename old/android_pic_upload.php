<?php

/*
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect_autologout.php');
$mypottstatus = mysqli_real_escape_string($mysqli, $_POST["pottstatus"]);
$myid = mysqli_real_escape_string($mysqli, $_POST["myid"]);
$mypass = mysqli_real_escape_string($mysqli, $_POST["mypass"]);
$query = "UPDATE  investor SET  profile_picture =  '$mypottstatus' WHERE  investor_id =  '5' ";
$result = $mysqli->query($query);
*/

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_FILES["image"]["name"]) && trim($_FILES["image"]["name"]) != "") {

require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');


$t = time();
$r_t = date("Y-m-d",$t);
$ext = $r_t . $t;

define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

if(isset($_POST["pottstatus"])){

	$mypottstatus = mysqli_real_escape_string($mysqli, $_POST["pottstatus"]);

} else {
	$mypottstatus = "I'm out here fishing...";
}
$myid = mysqli_real_escape_string($mysqli, $_POST["myid"]);
$mypass = mysqli_real_escape_string($mysqli, $_POST["mypass"]);

$mypottstatus = trim($mypottstatus);
$myid = trim($myid);
$mypass = trim($mypass);
$investor_id = $myid;

$query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

$result = $mysqli->query($query);

if (mysqli_num_rows($result) != 0) {

$row = $result->fetch_array(MYSQLI_ASSOC);
$dbpass = trim($row["password"]);
$dbflag = trim($row["flag"]);

if($mypass == $dbpass && $dbflag == 0){

$target_dir = "../pic_upload/uploads/";
$target_file = $target_dir . $ext . basename($_FILES["image"]["name"]);
$uploadOk = 1;

$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$db_picname = "uploads/" . $ext . "." . $imageFileType;

$target_file = "../pic_upload/" . $db_picname;

$target_pic = "../user/news_files/pics/";
$target_pic = $target_pic . $ext . basename($_FILES["image"]["name"]);
$pic_db_name = "news_files/pics/" . $ext . basename($_FILES["image"]["name"]);

// Check if image file is a actual image or fake image
//if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    /*
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
    */

    //echo "uploadOk : " . $uploadOk ;
//}
// Check if file already exists
if (file_exists("../pic_upload/" . $db_picname)) {

    $uploadOk = 0;
    $signUpReturn["datareturned"][0]  = array(

        'status' => "no", 
        'message' => "na",
        'error' => "File already exits"

        );
    echo json_encode($signUpReturn); exit;

}
// Check file size
if ($_FILES["image"]["size"] > 7 * MB) {
    $uploadOk = 0;
    $signUpReturn["datareturned"][0]  = array(

        'status' => "no", 
        'message' => "na",
        'error' => "File is too large"

        );
    echo json_encode($signUpReturn); exit;

}


// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $uploadOk = 0;
    $signUpReturn["datareturned"][0]  = array(

        'status' => "no", 
        'message' => "na",
        'error' => "Incorrect file"

        );
    echo json_encode($signUpReturn); exit;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
                $signUpReturn["datareturned"][0]  = array(

                    'status' => "no", 
                    'message' => "na",
                    'error' => "1 Something went Awry."

                    );
                echo json_encode($signUpReturn); exit;
} else {

        //$_FILES["image"]["name"] = $ext . $_FILES["image"]["name"];
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

      $pic_name = $db_picname;
      $query = "UPDATE  investor SET  profile_picture =  '$pic_name' WHERE  investor_id =  '$investor_id' ";
      $result = $mysqli->query($query);


      if ($result == "1") {

              //$query = "UPDATE  investor SET  status =  '$mypottstatus' WHERE  investor_id =  '$investor_id' ";
                //$result = $mysqli->query($query);

			copy($target_file, $target_pic);
            $news_id = uniqid($myid, TRUE);
            $date_time = date("Y-m-d H:i:s");
            $date_time = trim($date_time);

            $table_name = "newsfeed";
            $column1_name = "type";
            $column2_name = "inputtor_type";
            $column3_name = "inputtor_id";
            $column4_name = "news_id";
            $column5_name = "date_time";
            $column6_name = "news";
            $column7_name = "news_image";
            $column8_name = "news_video";
            $column9_name = "news_aud";
            $column10_name = "news_id_ref";

            $column1_value = "news";
            $column2_value = "investor";
            $column3_value = $myid;
            $column4_value = $news_id;
            $column5_value = $date_time;
            $column6_value = "I just changed my pott picture.";
            $column7_value = $pic_db_name;
            $column8_value = "";
            $column9_value = "";
            $column10_value = "";

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "s";
            $pam9 = "s";
            $pam10 = "s";
            include(ROOT_PATH . 'inc/insert10_prepared_statement.php');
                  include(ROOT_PATH . 'inc/db_connect.php');

                $return_pic = HTTP_HEAD . "://fishpott.com/pic_upload/" . $db_picname;
                $signUpReturn["datareturned"][0]  = array(

                    'status' => "yes", 
                    'message' => "Image Uploaded",
                    'picture' => $return_pic,
                    'error' => "na"

                    );
                echo json_encode($signUpReturn); exit;
      } else {

                $signUpReturn["datareturned"][0]  = array(

                    'status' => "no", 
                    'message' => "na",
                    'error' => "2 Something went Awry. We Couldn't Verify Your Pott"

                    );
                echo json_encode($signUpReturn); exit;

        }
    } else {

            $signUpReturn["datareturned"][0]  = array(

                    'status' => "no", 
                    'message' => "na",
                    'error' => "3 Something went Awry. We Couldn't Verify Your Pott"

                    );
                echo json_encode($signUpReturn); exit;

    }
}

} else {

            $signUpReturn["datareturned"][0]  = array(

                    'status' => "no", 
                    'message' => "na",
                    'error' => "4 Something went Awry. We Couldn't Verify Your Pott"

                    );
                echo json_encode($signUpReturn); exit;

}
}

}