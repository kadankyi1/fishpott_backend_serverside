<?php

  $query = "SELECT * FROM event WHERE sku = $i ";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $creater_id = $row["creater_id"];
      $event_name = $row["event_name"];
      $event_date = $row["event_date"];

      if($creater_id == $investor_id){
      
        $skip = "no";

      } else {
        $skip = "yes";

      }

  } else {

      $skip = "yes";
  }
