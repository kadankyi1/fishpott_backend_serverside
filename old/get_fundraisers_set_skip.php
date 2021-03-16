<?php

  $query = "SELECT * FROM fundraiser WHERE sku = $i AND f_starter_id = '$investor_id'";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $fundraiser_id = $row["fundraiser_id"];
      $fundraiser_name = $row["fundraiser_name"];
      $start_date = $row["start_date"];
      $end_date = $row["end_date"];
      $target_amount = $row["target_amount"];
      $num_of_contributors = $row["num_of_contributors"];
      $contributed_amount = $row["contributed_amount"];
      $skip = "no";

  } else {

      $skip = "yes";
  }
