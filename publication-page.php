<?php
  include("config.php");
  session_start();

  if (isset($_GET["p_id"]) && isset($_SESSION["email"])) {
    $p_id = $_GET["p_id"];
    $user_type = $_SESSION["type"];
  }else{
    header("location: index.php");
  }

  $sql = "SELECT publication_date, title, pages, downloads, p_name, email  FROM publication NATURAL JOIN submits WHERE p_id = '$p_id'";
  $info_result = mysqli_query($dbc,$sql);
  $count = mysqli_num_rows($info_result);

  if($count == 1){
    while ( $row = mysqli_fetch_array($info_result, MYSQLI_NUM)) {
        $publication_date = $row[0];
        $title = $row[1];
        $pages = $row[2];
        $downloads = $row[3];
        $publisher_name = $row[4];
        $email = $row[5];
    }

    $sql = "SELECT s_name, s_surname FROM subscriber WHERE email = '$email'";
    $name = mysqli_query($dbc, $sql);
    $name = mysqli_fetch_array($name, MYSQLI_NUM);
    $name = $name[0];

    $sql = "SELECT count(*) FROM cites WHERE cited = '$p_id'";
    $cited_result = mysqli_query($dbc,$sql);
    $num_of_citers = mysqli_fetch_array($cited_result, MYSQLI_NUM);

    $sql = "SELECT name, link FROM sponsor NATURAL JOIN finances NATURAL JOIN publication WHERE p_id='$p_id'";
    $result = mysqli_query($dbc,$sql);
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    $sponsor_name = $row[0];
    $sponsor_link = $row[1];

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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
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
            <?php
                //Subscriber
                if($user_type == 0){
                  //do nothing
                }else if($user_type == 1){
                  echo '<li class="nav-item">
                                <a class="nav-link" id="author-submission" href="author-submissions.php">Submissions</a>
                              </li>';
                }
                else if($user_type == 2){
                  echo '<li class="nav-item">
                          <a class="nav-link" id="submissions" href="#">Editor Submission</a>
                        </li>';
                }else{ //editor
                  echo '<li class="nav-item">
                          <a class="nav-link" id="reviewer-submission" href="reviewer-submission.php">Invitations</a>
                        </li>';
                }
             ?>
            <li class="nav-item">
              <a class="nav-link" id="navbar-logout" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    <br />
    <div class="container">
      <div class="jumbotron">
          <?php  echo "<h1 align='center'>$title</h1>"?>
          <br />
          <div class="row">
            <div class="col-md-5">
              <?php
                if(isset($completed)){
                  echo "<table>";
                  echo "<tr align='center'><th>Bibliography</th></tr>";
                  echo "<tr><td><strong>Publisher: </strong><a href='find-publisher.php?p_name=$publisher_name'>$publisher_name</a></td></tr>";
                  echo "<tr><td><div><strong>Publication Date:</strong> $publication_date </div></td></tr>";
                  echo "<tr><td><div><strong>Number of Pages:</strong> $pages </div></td></tr>";
                  echo "<tr><td><div><strong>Number of Citers:</strong> $num_of_citers[0] </div></td></tr>";
                  echo "<tr><td><div><strong>Number of Downloads: </strong> $downloads </div></td></tr>";
                  echo "<tr><td><div><strong>Authors: </strong> $name </div></td></tr>";
                  echo "<tr><td><div><strong>Sponsors: </strong><a href='$sponsor_link'>$sponsor_name</a></div></td></tr>";
                  echo "</table>";
                  }
               ?>
            </div>
            <div class="col-md-5">
              <?php
              if(isset($completed)) {
                echo "<table>";
                echo "<tr align='center'><th>Related publications</th></tr>";
                while ( $row = mysqli_fetch_array($citer_result, MYSQLI_NUM)) {
                  echo "<tr><td><a href='publication-page.php?p_id=$row[0]'>$row[1]</a></td></tr>";
                }
                echo "</table>";
              }
               ?>
            </div>
            <div class="col-md-2">
              <?php
              if(isset($completed)) {
                echo "<table>";
                echo "<tr align='center'><th>Download</th></tr>";
                echo "<tr><td><a href='documentlink.html'><i class='fas fa-arrow-alt-circle-down fa-7x'></i></a></td></tr>";
                echo "</table>";
              }
               ?>
            </div>
          </div>
      </div>
    </div>
  </body>
</html>
