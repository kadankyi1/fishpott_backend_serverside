<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8mb4');
    $today = date("F j, Y");
    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

                $query = "SELECT net_worth, coins_secure_datetime FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $last_login = trim($row["coins_secure_datetime"]);
                      $my_pearls = trim($row["net_worth"]);
                      $my_pearls = intval($my_pearls);

                      if($my_pearls < 10){
                        echo "Pott pearls are too low."; exit;
                      } else {

                        $shield_duration = "3";
                        //$shield_duration = strval($shield_duration);
                        $shield_duration_static = $shield_duration;

                        $shield_duration  = "+ " . $shield_duration . " days";
                        $last_login_new = date('Y-m-d',strtotime($shield_duration, strtotime($last_login)));
                        //$yield_date_time = date('Y-m-d', strtotime($yield_duration));
                    $query = "SELECT status FROM awiawia_day WHERE sku = 1";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $poach_day_status = trim($row["status"]);
                      $poach_day_status = intval($poach_day_status);

                      if($poach_day_status == 1){
                          $charge_pearls = $my_pearls*0.3;
                        //echo "my_pearls_old is 30% \n<br>";
                      } else {
                        $charge_pearls = $my_pearls*0.1;
                        //echo "my_pearls_old is 10% \n<br>";
                      }

                    } else {
                        echo "Shields are not available on Poach Day. You should have shielded earlier. You can also buy a shield after Poach Day"; exit;
                    }

                        $charge_pearls = intval($charge_pearls);

                        $my_pearls_new = $my_pearls - $charge_pearls;
/*
                        echo "poach_day_status : " . $poach_day_status . " \n<br>";
                        echo "charge_pearls : " . $charge_pearls . " \n<br>";
                        echo "my_pearls_old : " . $my_pearls . " \n<br>";
                        echo "my_pearls_new : " . $my_pearls_new . " \n<br>";
                        echo "last_login_old : " . $last_login . " \n<br>";
                        echo "last_login_new : " . $last_login_new . " \n<br>"; exit;
*/
                        $query = "UPDATE investor SET coins_secure_datetime = '$last_login_new', net_worth = '$my_pearls_new'  WHERE investor_id = '$myid'";
                        $result = $mysqli->query($query);

                          if($result == true){

                            $newDate = date("F j, Y", strtotime($last_login_new));

                            echo "Poach Shield has been set. Shield charge is " . $charge_pearls . " pott pearls. You currently have " . $my_pearls_new . " pott pearls. Your shield expires on " . $newDate;

                          } else {

                            echo "Something went awry";

                          }

                      }

                    } else {

                        echo "Something went awry"; exit;
                    }
            
                }


          }/////// END OF PASSWORD CHECK

        }
