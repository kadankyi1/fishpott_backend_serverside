<?php

  $query = "SELECT add_id, add_name FROM addressofmine WHERE sku = $i AND investor_id = '$investor_id'";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $add_id = $row["add_id"];
      $add_name = $row["add_name"];
      $skip = "no";

  } else {

      $skip = "yes";
  }
