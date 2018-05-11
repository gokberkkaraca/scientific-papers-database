<?php
  include("config.php");
  session_start();

  $p_name = $_GET["p_name"];

  $sql = "SELECT journal_topic  FROM publisher NATURAL JOIN journal WHERE p_name= '$p_name'";
  $info_result = mysqli_query($dbc,$sql);
  $count = mysqli_num_rows($info_result);

  if($count == 1){
    while ( $row = mysqli_fetch_array($info_result, MYSQLI_NUM)) {
        $journal_topic = $row[0];
    }
    $sql = "SELECT volume_no FROM journal_volume WHERE p_name='$p_name' ORDER BY volume_no ASC";
    $journal_volumes = mysqli_query($dbc, $sql);
    $number_of_volumes = mysqli_num_rows($journal_volumes);

    if (isset($_GET["volume"])) {
      $volume = $_GET["volume"];
      $sql = "SELECT p_id, title FROM published_in NATURAL JOIN publication WHERE volume_no = '$volume' AND p_name='$p_name'";
      $result = mysqli_query($dbc,$sql);
    }
    $completed = "1";
  }else{
    header("location: notfound.html");
  }
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>Journal Page</title>
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
          <div class="col-md-2">
          </div>
          <div class="col-md-4" align="center">
            <?php
              if(isset($completed)){
                echo '<table>
                      <tr align="center"><th>Details</th></tr>
                      <tr><td><strong>Journal Topic: </strong>'.$journal_topic.'</td></tr>
                      <tr><td>';
                for ($i = 1; $i <= $number_of_volumes; $i++) {
                  echo "<a href='journal-page.php?p_name=$p_name&volume=$i'>Volume $i</a><br />";
                }
                echo  '</td></tr>
                      </table>';
                }
             ?>
          </div>
          <div class="col-md-4" align="center">
            <?php
            if(isset($completed)) {
              echo "<table>";
              if (isset($result)) {
                echo "<tr align='center'><th>Publications</th></tr>";
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                  echo "<tr><td><a href='publication-page.php?p_id=$row[0]'>$row[1]</a></td></tr>";
                }
              }
              echo "</table>";
            }
             ?>
          </div>
          <div class="col-md-2">
          </div>
      </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>
