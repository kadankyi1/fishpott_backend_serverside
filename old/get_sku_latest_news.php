<?php

  $query = "SELECT sku FROM newsfeed ORDER BY sku DESC ";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $latest_sku = $row["sku"];
    } else {

      $latest_sku = 0;
    }

//echo $latest_sku; exit;