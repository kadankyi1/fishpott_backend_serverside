<?php
if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['token']) && trim($_POST['token']) != "") {

  require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $token = mysqli_real_escape_string($mysqli, $_POST['token']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $token = trim($token);

  $like_data_time = date("Y-m-d H:i:s");

    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          if($mypass == $dbpass && $flag == 0) {

        $query = "SELECT coins_secure_datetime, net_worth FROM investor WHERE investor_id = '$myid'";   

            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $last_login = trim($row["coins_secure_datetime"]);
                  $net_worth = trim($row["net_worth"]);

                  echo $net_worth;

                  $now = time(); // or your date as well
                  $your_date = strtotime($last_login);
                  $datediff = $now - $your_date;

                  $diff = (floor($datediff / (60 * 60 * 24)) + 1);

                  if($diff > 1){

                    //echo "changing date";
                if(isset($_POST["just_date_update"]) && trim($_POST["just_date_update"])  == "yes"){

                  $query = "UPDATE investor SET coins_secure_datetime = '$like_data_time'  WHERE investor_id = '$myid'";
                  $result = $mysqli->query($query);

                } else {

                  $query = "UPDATE investor SET fcm_token_web = '$token', coins_secure_datetime = '$like_data_time'  WHERE investor_id = '$myid'";
                  $result = $mysqli->query($query);

                }


            
                  } else {

                   // echo "NOT changing date 1";
                    if(!isset($_POST["just_date_update"])){

                      $query = "UPDATE investor SET fcm_token_web = '$token' WHERE investor_id = '$myid'";
                      $result = $mysqli->query($query);

                    }


                  }



              } else {
                    
                if(!isset($_POST["just_date_update"])){

                  $query = "UPDATE investor SET fcm_token_web = '$token' WHERE investor_id = '$myid'";
                  $result = $mysqli->query($query);
                }
              }

          }

        }

    }

