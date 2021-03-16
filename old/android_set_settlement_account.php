<?php
/*********************************************************************************

      NEW SHARES ARE NAMED BY ADDING THE PARENT SHARES ID, AN UNDERSCORE, THE POTT NAME, AN UNDERSCORE, THE START DATE, AN UNDERSCORE AND END DATE


**********************************************************************************/
if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['raw_pass']) && trim($_POST['raw_pass']) != "" && 
	isset($_POST['acc_type']) && trim($_POST['acc_type']) != "" && 
	isset($_POST['acc_country']) && trim($_POST['acc_country']) != "" && 
	isset($_POST['bank_routing_number']) && 
	isset($_POST['bank_name_or_mobilenetwork_name']) && trim($_POST['bank_name_or_mobilenetwork_name']) != "" && 
	isset($_POST['bank_acc_or_mobilemoney_number']) && trim($_POST['bank_acc_or_mobilemoney_number']) != ""
) {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $raw_pass = mysqli_real_escape_string($mysqli, $_POST['raw_pass']);
    $settle_type = mysqli_real_escape_string($mysqli, $_POST['acc_type']);
    $settle_country = mysqli_real_escape_string($mysqli, $_POST['acc_country']);
    $acc_bank_rou_num = mysqli_real_escape_string($mysqli, $_POST['bank_routing_number']);
    $bank_network_name = mysqli_real_escape_string($mysqli, $_POST['bank_name_or_mobilenetwork_name']);
    $acc_bank_mm_num = mysqli_real_escape_string($mysqli, $_POST['bank_acc_or_mobilemoney_number']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $raw_pass = trim($raw_pass);
    $settle_type = trim($settle_type);
    $settle_country = trim($settle_country);
    $acc_bank_rou_num = trim($acc_bank_rou_num);
    $bank_network_name = trim($bank_network_name);
    $acc_bank_mm_num = trim($acc_bank_mm_num);


  $password = $raw_pass;

  include(ROOT_PATH . 'inc/pw_fold.php');

    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0 && $e_password == $dbpass ) {

		$query = "SELECT settle_type FROM fa_misika_faha WHERE investor_id = '$investor_id'";   
		$result = $mysqli->query($query);
				
		if (mysqli_num_rows($result) != 0) {

			//$query = "UPDATE fa_misika_faha SET settle_type = $settle_type, country = $settle_country, receiver_institution_name = $bank_network_name, b_acc_num_mm_num = $acc_bank_mm_num, routing_number = $acc_bank_rou_num  WHERE investor_id = '$investor_id'";

			$table_name = "fa_misika_faha";
			$column1_name = "routing_number";
			$column2_name = "settle_type";
			$column3_name = "country";
			$column4_name = "receiver_institution_name";
			$column5_name = "b_acc_num_mm_num";
			$row_check = "investor_id";

			$column1_value = $acc_bank_rou_num;
			$column2_value = $settle_type;
			$column3_value = $settle_country;
			$column4_value = $bank_network_name;
			$column5_value = $acc_bank_mm_num;
			$row_check_value = $investor_id;

			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			$pam4 = "s";
			$pam5 = "s";
			$pam6 = "s";

			include(ROOT_PATH . 'inc/update5_where1_prepared_statement.php');


			if ($done == 1) {

                  echo "Account Set Completed"; exit;

			} else {

                  echo "Something went awry . Error code 5"; exit;

			}

		} else {

			$table_name = "fa_misika_faha";
			$column1_name = "investor_id";
			$column2_name = "settle_type";
			$column3_name = "country";
			$column4_name = "receiver_institution_name";
			$column5_name = "b_acc_num_mm_num";
			$column6_name = "routing_number";

			$column1_value = $investor_id;
			$column2_value = $settle_type;
			$column3_value = $settle_country;
			$column4_value = $bank_network_name;
			$column5_value = $acc_bank_mm_num;
			$column6_value = $acc_bank_rou_num;

			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			$pam4 = "s";
			$pam5 = "s";
			$pam6 = "s";

			include(ROOT_PATH . 'inc/insert6_prepared_statement.php');

			if($done == 1) {

                  echo "Account Set Completed"; exit;


			} else {

                  echo "Something went awry . Error code 4"; exit;
			
			}

		}


          } else {
                  echo "Something went awry. Error code 3"; exit;
    }


        } else {
                  echo "Something went awry. Error code 2"; exit;
    }


    } else {
                  echo "Something went awry . Error code 1"; exit;
    }
