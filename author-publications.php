<?php

      require_once('config.php');
      session_start();

      if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
        $user_type = $_SESSION['type'];
      }else{
        header("location:index.php");
      }
 ?>
<html>
    <head>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
            <link rel="stylesheet" href="css/author-submissions.css" />
            <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
            <script src="js/author-submissions.js"></script>
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
      </body>
    </html>
