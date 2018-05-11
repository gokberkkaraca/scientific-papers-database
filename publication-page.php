<?php
  include("config.php");
  session_start();

  $p_id = $_GET["p_id"];

  $sql = "SELECT publication_date, title, pages, downloads FROM publication WHERE p_id = '$p_id'";
  $info_result = mysqli_query($dbc,$sql);
  $count = mysqli_num_rows($info_result);

  if($count == 1){
    while ( $row = mysqli_fetch_array($info_result, MYSQLI_NUM)) {
        $publication_date = $row[0];
        $title = $row[1];
        $pages = $row[2];
        $downloads = $row[3];
    }

    $sql = "SELECT count(*) FROM cites WHERE cited = '$p_id'";
    $cited_result = mysqli_query($dbc,$sql);
    $num_of_citers = mysqli_fetch_array($cited_result, MYSQLI_NUM);

    $sql = "SELECT p_id, title FROM publication WHERE p_id IN (SELECT citer FROM cites WHERE cited = '$p_id')";
    $citer_result = mysqli_query($dbc,$sql);
    $completed = "1";
  }else{
    header("location: notfound.html");
  }

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Publication Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  </head>
  <body>
    <h1><?php  echo $title ?></h1>
    <div class="row">
      <div class="col-md-5 border">
        <?php
          if(isset($completed)){
            echo "<div>Bibliography</div>";
            echo "<div>Publication Date: $publication_date </div>";
            echo "<div>Number of Pages: $pages </div>";
            echo "<div>Number of Citers: $num_of_citers[0] </div>";
            echo "<div>Number of Downloads $downloads </div>";
            echo "<div>Authors TODO </div>";
            echo "<div>Sponsors TODO </div>";
            }
         ?>
      </div>
      <div class="col-md-5 border">
        Citers here
      </div>
      <div class="col-md-2 border">
        Download here
      </div>
    </div>
  </body>
</html>
