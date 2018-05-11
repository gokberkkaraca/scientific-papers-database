	<?php
		include("config.php");
		session_start();

		if (isset($_SESSION["email"])) {
			$email = $_SESSION["email"];
			$type = $_SESSION["type"];
		}
		// else {
		// 	header("location: signin.php");
		// }
		if(isset($_GET["search-key"])) {
			$search_key = $_GET["search-key"];
			$search_type = $_GET["search-type"];
			if ($search_type == "publication") {
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE title LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
			}else if ($search_type == "publisher") {
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE p_name LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
			}else{
			echo "3";
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE s_name LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
				echo "3";
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
								<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
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
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<br>
								<input type="text" name="search-key" placeholder="Type keyword here..." id="search-bar" required class="rounded">
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
						echo "<tr>";
						echo "<th scope=\"col\">Publication</th>";
						echo "<th scope=\"col\">Publisher</th>";
						echo "<th scope=\"col\">Author</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						while ( $row = mysqli_fetch_array($result,MYSQLI_NUM)) {
							echo "<tr>";
							echo "<td><a href='publication-page.php?p_id=$row[0]'>$row[1]</a></td>";
							echo "<td><a href='find-publisher.php?p_name=$row[2]'>$row[2]</a></td>";
							echo "<td></td>";
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
