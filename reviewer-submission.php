<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<link rel="stylesheet" href="css/main.css">
	<link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700" rel="stylesheet">
<script type="text/javascript">
	function writeFeedback(s_id) {
		alert(s_id);
	}
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
							<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
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
						<div class="col-md-12"><h1 id="scilib-title">Waiting for review</h1></div>
					</div>
				</form>
			</div>
	</div>
</div>
<?php
		include("config.php");

		function build_table($array){
			$i = 0;

	    	$html = '<tr>';

	    	$html .= '<td><a href="' . $array['doc_link'] . '">' . $array['title'] .'</td>';
	    	$html .= '<td>' . htmlspecialchars($array['date']) . '</td>';
	    	$html .= '<td>' . htmlspecialchars($array['p_name']) . '</td>';
	        $html .= '<td>' . '<button onclick="' . "writeFeedback($array[s_id]);" . '">Write Feedback</button>' . '</td>';

	        $html .= '</tr>';

		    return $html;
		}

		session_start();
		if (isset($_SESSION["email"])) {
			$email = $_SESSION["email"];

			// formulate the query
			$query = 	"SELECT * FROM invites AS I JOIN submission AS S JOIN submits as S2";
						//WHERE I.s_id = S.s_id
						//AND S.s_id = S2.s_id
						//AND S.email = S2.email

			// perform the query
			$result = mysqli_query($dbc,$query);

			// check number of rows to see if table is empty
	        $num_rows = mysqli_num_rows($result);

	      

	    	echo "<div id=\"result-panel\" align=\"center\">";
			echo "<div align=\"center\">";
			echo "<table class=\"table table-striped\">";
			echo "<thead class=\"thead-light\">";
			echo "<tr>";
			echo "<th scope=\"col\">Title</th>";
			echo "<th scope=\"col\">Date</th>";
			echo "<th scope=\"col\">Publisher Name</th>";
			echo "<th scope=\"col\">Action</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    echo build_table(array('title'=>$row["title"], 'doc_link'=>$row["doc_link"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 's_id'=>$row["s_id"]));
			}
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
			echo "</div>";
		}
	?>
</body>
</html>
