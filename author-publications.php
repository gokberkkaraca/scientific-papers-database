<?php

      require_once('config.php');
      session_start();

      if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
        $user_type = $_SESSION['type'];
      }else{
        header("location:index.php");
      }

      if (isset($_GET['email'])) {
        $email = $_GET['email'];
      }

      $sql = "SELECT s_name, s_surname FROM subscriber WHERE email='$email'";
      $author_name = mysqli_query($dbc, $sql);
      $author_name = mysqli_fetch_array($author_name, MYSQLI_NUM);

      $call = mysqli_prepare($dbc, 'CALL find_author_total_citations(?, @total_citations)');
      mysqli_stmt_bind_param($call, 's', $email);
      mysqli_stmt_execute($call);

      $select = mysqli_query($dbc, 'SELECT @total_citations');
      $result = mysqli_fetch_assoc($select);
      $total_citations  = $result['@total_citations'];

      $sql = "SELECT title, p_name, p_id FROM publication NATURAL JOIN submits WHERE email='$email' UNION SELECT title, p_name, p_id FROM co_authors, publication, submits WHERE co_authors.s_id = publication.s_id AND publication.s_id = submits.s_id AND co_authors.email = '$email'";
      $all_publications = mysqli_query($dbc, $sql);

      $total_publications = mysqli_num_rows($all_publications);

      $sql = "SELECT title FROM (SELECT cited as p_id, count(*) as cite_count FROM cites GROUP BY cited) AS cite_count_table NATURAL JOIN publication NATURAL JOIN submits WHERE email='$email' ORDER BY cite_count DESC LIMIT 1";
      $result = mysqli_query($dbc, $sql);
      $most_popular_publication = mysqli_fetch_array($result, MYSQLI_NUM);
 ?>
<html>
  <head>
          <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
  </head>
  <body>
    <div id="top-panel" align="center">
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
                 <a class="nav-link" id="navbar-logout" href="institutions.php">Institutions</a>
               </li>
							<li class="nav-item">
								<a class="nav-link" id="navbar-logout" href="logout.php">Logout</a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
      <div class="container">
        <div class="jumbotron">
        <h1><?php echo "$author_name[0] $author_name[1]"?></h1>
        <h4><?php echo "$email" ?></h4>
        <h6><?php echo "Total Publications: $total_publications, Total Citations: $total_citations" ?></h6>
        <h6><?php echo "Most Popular Publication: <i>$most_popular_publication[0]</i>" ?></h6>
        </div>
        <div>
          <h3>Publications of Author</h3>
          <table class="table table-striped">
            <?php
              if (mysqli_num_rows($all_publications)) {
                echo "<tr align='center'><th class='thead-light'>Publication</th><th>Publisher</th></tr>";
              }
              while ($row = mysqli_fetch_array($all_publications, MYSQLI_NUM)) {
                echo "<tr align='center'><td><a href='publication-page.php?p_id=$row[2]'>$row[0]</a></td><td><a href='find-publisher.php?p_name=$row[1]'>$row[1]</a></td></tr>";
              }
             ?>
          </table>
        </div>
      </div>

    </body>
  </html>
