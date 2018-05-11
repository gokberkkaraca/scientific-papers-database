<?php
  include("config.php");
  session_start();

  $p_name = $_GET["p_name"];

  $sql = "SELECT * FROM journal WHERE p_name= '$p_name'";

  $result = mysqli_query($dbc,$sql);
  $count = mysqli_num_rows($result);

  if($count == 1){
    header("location: journal-page.php?p_name=$p_name");
  }else{
    header("location: conference-page.php?p_name=$p_name");
  }
 ?>
