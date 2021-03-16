<?php
/*********************************************************************************

      NEW SHARES ARE NAMED BY ADDING THE PARENT SHARES ID, AN UNDERSCORE, THE POTT NAME, AN UNDERSCORE, THE START DATE, AN UNDERSCORE AND END DATE


**********************************************************************************/
if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['bness_name']) && trim($_POST['bness_name']) != "" && isset($_POST['bness_address']) && trim($_POST['bness_address']) != "" && isset($_POST['bness_pottname']) && trim($_POST['bness_pottname']) != "" && isset($_POST['bness_phone']) && trim($_POST['bness_phone']) != "" && isset($_POST['bness_password']) && trim($_POST['bness_password']) != "" && isset($_POST['bness_country']) && trim($_POST['bness_country']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);

    $bness_name = mysqli_real_escape_string($mysqli, $_POST['bness_name']);
    $bness_address = mysqli_real_escape_string($mysqli, $_POST['bness_address']);
    $bness_country = mysqli_real_escape_string($mysqli, $_POST['bness_country']);
    $bness_pottname = mysqli_real_escape_string($mysqli, $_POST['bness_pottname']);
    $bness_phone = mysqli_real_escape_string($mysqli, $_POST['bness_phone']);

    $bness_password = mysqli_real_escape_string($mysqli, $_POST['bness_password']);
    $b_password = $bness_password;

  include(ROOT_PATH . 'inc/b_pw_fold.php');
    $myid = trim($myid);
    $mypass = trim($mypass);

    $b_name = trim($bness_name);
    $head_office_address = trim($bness_address);
    $b_country = trim($bness_country);
    $b_pot_name = trim($bness_pottname);
    $b_phone = trim($bness_phone);
    $b_password = trim($bness_password);
    $date_started =  date("Y-m-d");
    $b_login_type = "phone";
    $b_user_id = $b_phone;

    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0 ) {


            ////////START INNER

                //$b_country = trim($i_country);
                if($b_country == "Ghana") {

                  $currency = "GHS";

                } elseif($b_country == "United Kingdom"){

                  $currency = "GBP";

                } else {

                  $currency = "USD";

                }

                $b_phone_length = strlen($b_phone);

                if($b_phone_length > 15) {
                  
                  echo "Something went awry. Error code 7"; exit;

                }

                $b_email = $b_pot_name . "@fishpot.com";
                $check = $b_phone;
                $column_name = "bness_phone";

                $user_table = "adwuma";
                $b_user_type = "business";

                include(ROOT_PATH . 'inc/check_user.php'); 
                  if(isset($create_account) && $create_account == "no"){ 

                        echo "This Phone/Email Has Already Been Used"; exit;
                  } 

                $business_id = uniqid($check, TRUE);

                    $table_name = "adwuma";

                    $column1_name = "investor_id";
                    $column2_name = "bness_id";
                    $column3_name = "bness_legal_name";
                    $column4_name = "bness_pot_name";
                    $column5_name = "bness_email";
                    $column6_name = "bness_phone";
                    $column7_name = "bness_addresss";
                    $column8_name = "bness_country";

                    $column1_value = $investor_id;
                    $column2_value = $business_id;
                    $column3_value = $b_name;
                    $column4_value = $b_pot_name;
                    $column5_value = $b_email;
                    $column6_value = $b_phone;
                    $column7_value = $head_office_address;
                    $column8_value = $b_country;

                    $pam1 = "s";
                    $pam2 = "s";
                    $pam3 = "s";
                    $pam4 = "s";
                    $pam5 = "s";
                    $pam6 = "s";
                    $pam7 = "s";
                    $pam8 = "s";
              
                    $done = 0;
                    include(ROOT_PATH . 'inc/insert8_prepared_statement.php');
                    include(ROOT_PATH . 'inc/db_connect.php');
                    if ($done == "1"){

                          $table_name = "investor";

                          $column1_name = "first_name";
                          $column2_name = "pot_name";
                          $column3_name = "dob";
                          $column4_name = "phone";
                          $column5_name = "email";
                          $column6_name = "investor_id";
                          $column7_name = "country";
                          $column8_name = "net_worth";
                          $column9_name = "coins_secure_datetime";
                          $column10_name = "currency";

                          $column1_value = $b_name;
                          $column2_value = $b_pot_name;
                          $column3_value = $date_started;
                          $column4_value = $b_phone . "_" . $b_pot_name;
                          $column5_value = $b_email . "_" . $b_pot_name;
                          $column6_value = $business_id;
                          $column7_value = $b_country;
                          $column8_value = 20;
                          $column9_value = date("Y-m-d H:i:s");
                          $column10_value = $currency;

                          $pam1 = "s";
                          $pam2 = "s";
                          $pam3 = "s";
                          $pam4 = "s";
                          $pam5 = "s";
                          $pam6 = "s";
                          $pam7 = "s";
                          $pam8 = "i";
                          $pam9 = "s";
                          $pam10 = "s";
                    
                          $done = 0;


                          include(ROOT_PATH . 'inc/insert10_prepared_statement.php');
                          include(ROOT_PATH . 'inc/db_connect.php');

                          if($done == 1){

                                include(ROOT_PATH . 'inc/b_id_fold.php');

                                $table_name = "wuramu";

                                $column1_name = "flag";
                                $column2_name = "id";
                                $column3_name = "number_login";
                                $column4_name = "email_login";
                                $column5_name = "password";
                                $column6_name = "login_type";
                                $column7_name = "full_name";

                                $column1_value = 0;
                                $column2_value = $business_id;
                                $column3_value = $b_phone;
                                $column4_value = $b_email;
                                $column5_value = $b_e_password;
                                $column6_value = "business";
                                $column7_value = $b_name;

                                $pam1 = "i";
                                $pam2 = "s";
                                $pam3 = "s";
                                $pam4 = "s";
                                $pam5 = "s";
                                $pam6 = "s";
                                $pam7 = "s";

                                $done = 0;
                                include(ROOT_PATH . 'inc/insert7_prepared_statement.php');
                                include(ROOT_PATH . 'inc/db_connect.php');

                                if($done == 1){

                                  echo "1";

                                } else {
                            echo "Something went awry. Try again later 6"; exit;
                                }                           


                          } else {
                            echo "Something went awry. Error code 5"; exit;
                          }



                    } else {

                        echo "Something went awry. Error code 4"; exit;
                    }


            //////////////// END INNER
          } else {
                  echo "Something went awry. Error code 3"; exit;
    		  }


        } else {
                  echo "Something went awry. Error code 2"; exit;
    	}


    } else {
                  echo "Something went awry . Error code 1"; exit;
    }
