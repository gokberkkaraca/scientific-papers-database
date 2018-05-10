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
				echo "1";
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE title LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
				echo "1";
			}else if ($search_type == "publisher") {
				echo "2";
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE p_name LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
				echo "2";
			}else{
			echo "3";
				$sql = "SELECT p_id, title, p_name FROM publication NATURAL JOIN submits NATURAL JOIN author NATURAL JOIN subscriber WHERE s_name LIKE '%$search_key%'";
				$result = mysqli_query($dbc,$sql);
				echo "3";
			}
		}
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Main Page</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
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
			<div align="center">
			<form name="search" id="search-form">

				<div class="row">
					<div class="col-md-12"><h1 > SCILIB </h1></div>
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
						<input type="text" name="search-key" placeholder="Any input to search" id="search-bar" required>
						<br>
						<br>
						<button id="search-button" class="btn btn-primary btn-sm" type="submit">Search</button>
					</div>
				</div>
			</form>
		</div>
			<br>
			<div id="result-panel" align="center">
					<div align="center">
			<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">Publication ID</th>
		      <th scope="col">Title</th>
		      <th scope="col">Publisher</th>
		      <th scope="col"></th>
		    </tr>
		  </thead>
		  <tbody>
		  	<?php
		  		while ( $row = mysqli_fetch_array($result,MYSQLI_NUM)) {
		  			echo "<tr>";
		  			echo "<td>$row[0]</td>";
		  			echo "<td>$row[1]</td>";
		  			echo "<td>$row[2]</td>";
		  			echo "</tr>";
		  		}
		  	?>
		  </tbody>
		</table>
		</div>
			</div>
		</body>
		</html>
