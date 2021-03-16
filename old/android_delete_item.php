<?php
if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['news_type']) && trim($_POST['news_type']) != "" && isset($_POST['news_id']) && trim($_POST['news_id']) != "") {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $news_type = mysqli_real_escape_string($mysqli, $_POST['news_type']);
    $news_id = mysqli_real_escape_string($mysqli, $_POST['news_id']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $news_type = trim($news_type);
    $news_id = trim($news_id);

    if($news_type == "up4sale"){

    	$table_name = "up4sale";
    	$column_name1 = "up4sale_news_id";
    	$column_name2 = "seller_id";
    	
    } else if($news_type == "shares4sale") {

    	$table_name = "shares4sale";
    	$column_name1 = "shares_news_id";
    	$column_name2 = "shares4sale_owner_id";
    } else if($news_type == "event") {

    	$table_name = "event";
    	$column_name1 = "event_news_id";
    	$column_name2 = "creater_id";
    } else if($news_type == "fundraiser") {

    	$table_name = "fundraiser";
    	$column_name1 = "f_news_id";
    	$column_name2 = "f_starter_id";
    } else {
    	echo "Something went awry. Try again later."; exit;
    }

    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0) {

    $query = "SELECT adetor_type FROM adetor WHERE adetor_news_id = '$news_id'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);

                echo "There's been a transaction on this post. Deletion failed"; exit;          

          } else {


                    $query = "UPDATE $table_name SET flag = 1 WHERE $column_name1 = '$news_id' AND $column_name2 = '$investor_id'";
                    $result = $mysqli->query($query);

                    if($result == true){

                        $query = "UPDATE newsfeed SET flag = 1 WHERE news_id = '$news_id' AND inputtor_id = '$investor_id'";
                        $result = $mysqli->query($query);

                        if($result == true){

                            echo "Delete Completed"; exit;
                            

                        } else {
                            echo "Something went awry. Try again later."; exit;
                        }

                    } else {
                        echo "Something went awry. Try again later."; exit;
                    }


          }

          }

        }

    }
