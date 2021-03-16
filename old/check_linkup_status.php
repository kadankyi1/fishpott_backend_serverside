<?php 

  $query = "SELECT * FROM linkups WHERE (sender_id = '$investor_id' AND receiver_id = '$p_investor_id') OR (sender_id = '$p_investor_id' AND receiver_id = '$investor_id')";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $sender_id = $row["sender_id"];
      $receiver_id = $row["receiver_id"];
      $status = $row["status"];

    } else {

      $status = 2;
      
    }
?>