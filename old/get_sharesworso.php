<?php

  $query = "SELECT share_name, parent_company_name, country_origin FROM shares_worso WHERE sku = $latest_sku";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $share_name = $row["share_name"];
      $parent_company_name = $row["parent_company_name"];
      $country_origin = $row["country_origin"];

      $skip = "no";

  } else {

      $skip = "yes";
  }
