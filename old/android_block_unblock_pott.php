<?php
if(
    isset($_POST['myid']) && trim($_POST['myid']) != "" && 
    isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
    isset($_POST['block_status']) && trim($_POST['block_status']) != "" && 
    isset($_POST['blocked_pottname']) && trim($_POST['blocked_pottname']) != ""
) {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $block_status = mysqli_real_escape_string($mysqli, $_POST['block_status']);
    $blocked_pottname = mysqli_real_escape_string($mysqli, $_POST['blocked_pottname']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $block_status = trim($block_status);
    $blocked_pottname = trim($blocked_pottname);

    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);

          if($mypass == $dbpass && $flag == 0) {

                $this_blocked_id = $myid . "_" . $blocked_pottname;

                if($block_status == "1"){

                    $query = "DELETE FROM mern_ha_me_fuo WHERE block_action_id = '$this_blocked_id'";
                    $result = $mysqli->query($query);

                    if($result == true){

                        echo "1"; exit;
                        

                    } else {

                        echo "Something went awry. Try again later."; exit;

                    }


                } else {

                    $query = "SELECT investor_id FROM investor WHERE pot_name = '$blocked_pottname'";   

                    $result = $mysqli->query($query);
                        
                    if (mysqli_num_rows($result) != 0) {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $blocked_investor_id = trim($row["investor_id"]);

                            $table_name = "mern_ha_me_fuo";

                            $column1_name = "block_action_id";
                            $column2_name = "sender_id";
                            $column3_name = "blocked_id";
                            $column4_name = "blocked_pottname";
                            $column5_name = "block_date";


                            $column1_value = $this_blocked_id;
                            $column2_value = $myid;
                            $column3_value = $blocked_investor_id;
                            $column4_value = $blocked_pottname;
                            $column5_value = date('d-m-y');

                            $pam1 = "s";
                            $pam2 = "s";
                            $pam3 = "s";
                            $pam4 = "s";
                            $pam5 = "s";

                            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');


                            if($done == 1){
                                echo "1"; exit;
                            } else {
                                exit;
                            }

                      } else {
                        exit;
                      }



                }

          }

        }

    }
