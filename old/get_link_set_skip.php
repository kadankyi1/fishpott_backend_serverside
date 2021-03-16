<?php

  $query = "SELECT * FROM linkups WHERE (sku = $i AND sender_id = '$investor_id' AND status = 1) OR (sku = $i AND receiver_id = '$investor_id' AND status = 1)";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $sender_id = $row["sender_id"];
      $receiver_id = $row["receiver_id"];

      if($sender_id == $investor_id) {

        $co_investor_id = $receiver_id;

      } else {
        
        $co_investor_id = $sender_id;

      }


    $query = "SELECT first_name, last_name FROM investor WHERE investor_id = '$co_investor_id'";

        //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $link_first_name = $row["first_name"];
              $link_last_name = $row["last_name"];
              $co_full_name = $link_first_name . " " . $link_last_name;
              $skip = "no";
            } else{

              $skip = "yes";
            }
      } else {

      $skip = "yes";
      }
