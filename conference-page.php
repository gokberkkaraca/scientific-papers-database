<?php
  include("config.php");
  session_start();

  if (isset($_GET["p_name"]) && isset($_SESSION["email"])) {
    $p_name = $_GET["p_name"];
    $user_type = $_SESSION["type"];
  }else{
    header("location: index.php");
  }

  $sql = "SELECT date, conference_topic  FROM publisher NATURAL JOIN conference WHERE p_name= '$p_name'";
  $info_result = mysqli_query($dbc,$sql);
  $count = mysqli_num_rows($info_result);

  if($count == 1){
    while ( $row = mysqli_fetch_array($info_result, MYSQLI_NUM)) {
        $date = $row[0];
        $conference_topic = $row[1];
    }

    $sql = "SELECT p_id, title FROM publication NATURAL JOIN submits WHERE p_name='$p_name'";
    $publication_result = mysqli_query($dbc, $sql);
    $publication_count = mysqli_num_rows($publication_result);
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
                // Reviewer
                if($user_type == 1){
                  echo '<li class="nav-item">
                          <a class="nav-link" id="reviewer-submission" href="reviewer-submission.php">Invitations</a>
                        </li>';
                // Author
              }else if($user_type == 2){
                  echo '<li class="nav-item">
                                <a class="nav-link" id="author-submission" href="author-submissions.php">Submissions</a>
                              </li>';
                  echo '<li class="nav-item">
                                <a class="nav-link" id="author-submission" href="author-publications.php">Publications</a>
                              </li>';
                }
                // Editor
                else if($user_type == 3){
                  echo '<li class="nav-item">
                          <a class="nav-link" id="submissions" href="editor-submission.php">Editor Submission</a>
                        </li>';
                }else{ // Subscriber

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
          <?php  echo "<h1 align='center'>$p_name</h1>"?>
          <br />
          <div class="row">
            <div class="col-md-5">
              <?php
                if(isset($completed)){
                  echo "<table>";
                  echo "<tr align='center'><th>Details</th></tr>";
                  echo "<tr><td><strong>Number of Publications: </strong> $publication_count</td></tr>";
                  echo "<tr><td><strong>Date: </strong> $date</td></tr>";
                  echo "<tr><td><strong>Topic: </strong> $conference_topic</td></tr>";
                  echo "</table>";
                  }
               ?>
            </div>
            <div class="col-md-5">
              <?php
              if(isset($completed)) {
                echo "<table>";
                echo "<tr align='center'><th>Publications</th></tr>";
                while ( $row = mysqli_fetch_array($publication_result, MYSQLI_NUM)) {
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
                  echo "<tr align='center'><th>View Audience</th></tr>";
                  echo "<tr><td align='center'><a href='conference-audience.php?p_name=$p_name'><i class='fas fa-user fa-5x'></i></a></td></tr>";
                  echo "</table>";
                }
               ?>
            </div>
          </div>
      </div>
    </div>
  </body>
</html>
