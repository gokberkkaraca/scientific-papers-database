	<?php
		include("config.php");
		session_start();

		if (isset($_SESSION["email"])) {
			$email = $_SESSION["email"];
			$user_type = $_SESSION["type"];
		}
		else {
		 	header("location: index.php");
		}

		if (isset($_GET["from-date"])) {
			$from_date = $_GET["from-date"];
			$from_month = strtok($from_date, "/");
			$from_day = strtok("/");
			$from_year = strtok("/");
			$from_date = $from_year."-".$from_month."-".$from_day;
		}

		if (isset($_GET["to-date"])) {
			$to_date = $_GET["to-date"];
			$to_month = strtok($to_date, "/");
			$to_day = strtok("/");
			$to_year = strtok("/");
			$to_date = $to_year."-".$to_month."-".$to_day;
		}

		if(isset($_GET["search-key"])) {
			$search_key = $_GET["search-key"];
			$search_type = $_GET["search-type"];
			if ($search_type == "publication") {
				$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE title LIKE '%$search_key%'";
				if ($from_date != "--") {
					$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE title LIKE '%$search_key%' AND DATE(publication_date) >= '$from_date'";
				}
				if ($from_date != "--" && $to_date != "--") {
					$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE title LIKE '%$search_key%' AND DATE(publication_date) BETWEEN '$from_date' AND '$to_date'";
				}
				$result = mysqli_query($dbc,$sql);
			}else if ($search_type == "publisher") {
				$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE p_name LIKE '%$search_key%'";
				if ($from_date != "--") {
					 $sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE p_name LIKE '%$search_key%' AND DATE(publication_date) >= '$from_date'";
				}
				if ($from_date != "--" && $to_date != "--") {
					$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE p_name LIKE '%$search_key%' AND DATE(publication_date) BETWEEN '$from_date' AND '$to_date'";
				}
				$result = mysqli_query($dbc,$sql);
			}else{
				$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE s_name LIKE '%$search_key%'";
				if ($from_date != "--") {
					$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE s_name LIKE '%$search_key%' AND DATE(publication_date) >= '$date'";
				}
				if ($from_date != "--" && $to_date != "--") {
					$sql = "SELECT p_id, title, p_name, s_name, s_surname, publication_date, email FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE s_name LIKE '%$search_key%' AND DATE(publication_date) BETWEEN '$from_date' AND '$to_date'";
				}
				$result = mysqli_query($dbc,$sql);
			}
			$search_completed = 1;
		}
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Main Page</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		<link rel="stylesheet" href="css/main.css">
		<link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700" rel="stylesheet">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	  <link rel="stylesheet" href="/resources/demos/style.css">
	  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	  <script>
	  $( function() {
	    $( "#datepicker-from" ).datepicker();
			$( "#datepicker-to" ).datepicker();
	  } );
	  </script>
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
			<div class="container-fluid">
			  <div class="jumbotron">
					<form name="search" id="search-form">

						<div class="row">
							<div class="col-md-12"><h1 id="scilib-title">SCILIB</h1></div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="by-publication" name="search-type" class="custom-control-input" value="publication" checked="true" required >
									<label class="custom-control-label" for="by-publication">By Publication Name</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="by-publisher" name="search-type" class="custom-control-input" value="publisher">
									<label class="custom-control-label" for="by-publisher">By Publisher Name</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="by-author" name="search-type" class="custom-control-input" value="author">
									<label class="custom-control-label" for="by-author">By Author Name</label>
								</div>
								<div>
									<label for="from-date">From: </label>
									<input type="text" id="datepicker-from" name="from-date" class="rounded" style="margin-top: 10px">
									<label for="to-date">To: </label>
									<input type="text" id="datepicker-to" name="to-date" class="rounded" style="margin-top: 10px">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<br>
								<input type="text" name="search-key" placeholder="Type keyword here..." id="search-bar" class="rounded">
								<br>
								<br>
								<button id="search-button" class="btn btn-primary btn-sm" type="submit">Search</button>
							</div>
						</div>
					</form>
					</div>
			  </div>
				<?php
					if(isset($search_completed)){
						echo "<div id=\"result-panel\" align=\"center\">";
						echo "<div align=\"center\">";
						echo "<table class=\"table table-striped\">";
						echo "<thead class=\"thead-light\">";
						echo "<tr align='center'>";
						echo "<th scope=\"col\">Publication</th>";
						echo "<th scope=\"col\">Publisher</th>";
						echo "<th scope=\"col\">Author</th>";
						echo "<th scope=\"col\">Publication Date</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						while ( $row = mysqli_fetch_array($result,MYSQLI_NUM)) {
							echo "<tr align='center'>";
							echo "<td><a href='publication-page.php?p_id=$row[0]'>$row[1]</a></td>";
							echo "<td><a href='find-publisher.php?p_name=$row[2]'>$row[2]</a></td>";
							echo "<td><a href='author-publications.php?email=$row[6]'>$row[3] $row[4]</a></td>";
							echo "<td>$row[5]</td>";
							echo "</tr>";
						}
						echo "</tbody>";
						echo "</table>";
						echo "</div>";
						echo "</div>";
					}
				?>
			<div align="center">
		</body>
		</html>
