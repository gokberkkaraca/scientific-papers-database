<?php
  require('config.php');
  session_start();

  if (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $user_type = $_SESSION["type"];
  }
  else {
    header("location: index.php");
  }

  $sql = "SELECT p_name, start_date, end_date FROM subscription Where email = '$email'";
  $result = mysqli_query($dbc, $sql);
  
 ?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Subscriptions</title>
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
               <a class="nav-link" id="navbar-subscriptions" href="subscriptions.php">My Subscriptions</a>
             </li>
            <?php
                // Reviewer
                if($user_type == 1){
                  echo '<li class="nav-item">
                          <a class="nav-link" id="reviewer-submission" href="reviewer-submission.php">My Invitations</a>
                        </li>';
                // Author
              }else if($user_type == 2){
                  echo '<li class="nav-item">
                                <a class="nav-link" id="author-submission" href="author-submissions.php">My Submissions</a>
                              </li>';
                  echo '<li class="nav-item">
                                <a class="nav-link" id="author-submission" href="author-publications.php">My Publications</a>
                              </li>';
                }
                // Editor
                else if($user_type == 3){
                  echo '<li class="nav-item">
                          <a class="nav-link" id="submissions" href="editor-submission.php">My Submission</a>
                        </li>';
                }else{ // Subscriber

                }
             ?>
             <li class="nav-item">
               <a class="nav-link" id="navbar-institution" href="institutions.php">Institutions</a>
             </li>
             <li class="nav-item">
               <a class="nav-link" id="navbar-conferences" href="conferences.php">Conferences</a>
             </li>
          </ul>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" id="navbar-email" href="#"><i><?php echo $_SESSION['email']; ?></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="navbar-logout" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    <div class="container" style="margin-top: 30px">
      <?php
          echo "<div id=\"result-panel\" align=\"center\">";
          echo "<div align=\"center\">";
          echo "<table class=\"table table-striped\">";
          echo "<thead class=\"thead-light\">";
          echo "<tr align='center'>";
          echo "<th scope=\"col\">Journal Name</th>";
          echo "<th scope=\"col\">Subscription Start Date</th>";
          echo "<th scope=\"col\">Subscription End Date</th>";
          echo "<th scope=\"col\">Total Subscriptions to Journal</th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";
          while ( $row = mysqli_fetch_array($result,MYSQLI_NUM)) {
            echo "<tr align='center'>";
            echo "<td><a href='find-publisher.php?p_name=$row[0]'>$row[0]</a></td>";
            echo "<td>$row[1]</td>";
            echo "<td>$row[2]</td>";
            $sql = "SELECT count(p_name) as total_subscriptions FROM subscription WHERE p_name='$row[0]'";
            $total_subscriptions = mysqli_query($dbc, $sql);
            $total_subscriptions = mysqli_fetch_array($total_subscriptions, MYSQLI_NUM);

            echo "<td>$total_subscriptions[0]</td>";
            echo "</tr>";
          }
          echo "</tbody>";
          echo "</table>";
          echo "</div>";
          echo "</div>";
      ?>
    </div>
  </body>
</html>
