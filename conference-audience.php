<?php
  include("config.php");
  session_start();

  if (isset($_GET["p_name"]) && isset($_SESSION["email"])) {
    $p_name = $_GET["p_name"];
  }else{
    header("location: index.php");
  }


  $sql = "SELECT a_name, a_surname FROM audience WHERE p_name = '$p_name'";
  $audience_table = mysqli_query($dbc,$sql);
  $num_of_audience = mysqli_num_rows($audience_table);

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Conference Audience</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  </head>
  <body>
    <div id="nav-bar">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Scilib</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <a class="nav-link" href="main.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="submissions" href="#">Submissions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="navbar-logout" href="#">Logout</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    <br />
    <div class="container">
      <div class="jumbotron">
          <?php  echo "<h1 align='center'>$p_name</h1>"?>
          <br />
          <div class="row">
            <div class="col-md-12">
              <?php
                  echo "<div align='center'>";
                  echo "<table>";
                  echo "<tr align='center'><th>Audience</th></tr>";
                  $i = 1;
                  for ($i; $i <= $num_of_audience; $i++) {
                    $row = mysqli_fetch_array($audience_table, MYSQLI_NUM);
                    echo "<tr><td align='center'>$row[0] $row[1]</td></tr>";
                  }
                  echo "</table>";
                  echo "</div><br />";
                  echo "<p>Total audience: $num_of_audience</p>";
               ?>
            </div>
          </div>
      </div>
    </div>
  </body>
</html>
