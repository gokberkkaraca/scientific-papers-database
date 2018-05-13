<?php
	include("config.php");
	session_start();
	if (isset($_SESSION['email'])) {
		$user_type = $_SESSION['type'];
	}
 ?>
<html>
<head>

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
						<?php
								//Subscriber
								if($user_type == 0){
									//do nothing
								}else if($user_type == 1){ //reviewer
									echo '<li class="nav-item">
																<a class="nav-link" id="author-submission" href="#">Submissions</a>
															</li>';
								}
								else if($user_type == 2){ //reviewer
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
	</div>
	<div class="container-fluid" align="center">
		  <div class="jumbotron">
				<form name="editor-submission" id="editor-submission-form">

					<div class="row">
						<div class="col-md-12"><h1 id="scilib-title">Submissions</h1></div>
					</div>
				</form>
			</div>
	</div>
	<script type="text/javascript">
		
		function invite_reviewer(s_id, status) {
    		
  		}

  		function see_reviewers(s_id) {
    		
  		}

  		function see_feedback(s_id) {
    		
  		}

  		function send_for_approval(s_id) {
    		
  		}
	</script>
	<?php
	    if(isset($_GET['reject'])) {
	      $rev_email = $_SESSION['email'];
	      $sid = $_GET['s_id'];
	      $edit_email = $_GET['editor_email'];


	      $sql = "DELETE FROM invites WHERE reviewer_email='$rev_email' AND editor_email='$edit_email' AND s_id='$sid'";
	      mysqli_query($dbc, $sql);
	    }

		function build_table($array) {
			$i = 0;
	    	$email = $_SESSION["email"];

	    	$html = "<tr align='center'>";

	    	$html .= '<td><a href="' . $array['doc_link'] . '">' . $array['title'] .'</td>';
	    	$html .= '<td>' 
	    				. htmlspecialchars($array['s_name'])
						. " "
						. htmlspecialchars($array['s_surname'])
						. '</td>';

	    	$html .= '<td>' . htmlspecialchars($array['date']) . '</td>';
	    	$html .= '<td>' . htmlspecialchars($array['p_name']) . '</td>';
	      	
	      	// new submission -> invite reviewer
	    	if ($array['status'] == 0) {
	    		$html .= '<td>' 
	    				. '<button class="btn btn-info" onclick="' . "invite_reviewer('$array[s_id]', '0')" 
	    				. '">Invite Reviewer</button>' 
	    				. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>";
	    	}

	    	// waiting for feedback -> see reviewers
	    	else if ($array['status'] == 1) {
	    		$html .= '<td>' 
	    				. '<button class="btn btn-info" onclick="' . "see_reviewers('$array[s_id]');" 
	    				. '">See Reviewers</button>' 
	    				. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>";
	    	}

	    	// ready for approval -> send for approval
	    	else if ($array['status'] == 2) {
	    		$html .= '<td>' 
	    				. '<button class="btn btn-info" onclick="' . "see_feedback('$array[s_id]');" 
	    				. '">See feedback</button>'
	    				. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>"
	    				. '<p></p>' 
	    				. '<button class="btn btn-info" onclick="' . "send_for_approval('$array[s_id]');" 
	    				. '">Send for approval</button>';
	    	}

	    	// nothing for waiting for approval (status == 3)
	    	// the row will disappear after approval

	      	$html .= '</tr>';

		    return $html;
		}

		function start_table() {
			echo "<div id=\"result-panel\" align=\"center\">";
			echo "<div align=\"center\">";
			echo "<table class=\"table table-striped\">";
			echo "<thead class=\"thead-light\">";
			echo "<tr align='center'>";
			echo "<th scope=\"col\">Title</th>";
			echo "<th scope=\"col\">AuthorName</th>";
			echo "<th scope=\"col\">Date</th>";
			echo "<th scope=\"col\">Publisher</th>";
			echo "<th scope=\"col\">Action</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
		}

		if (isset($_SESSION["email"])) {
			$email = $_SESSION["email"];
	  		$user_type = $_SESSION["type"];
			
			// formulate the query
			$query =	"SELECT S.s_id, S.title, S.doc_link, S3.s_name, S3.s_surname, S.date,
						S2.p_name 
						FROM submission AS S JOIN submits AS S2 JOIN subscriber AS S3 
						WHERE S.s_id = S2.s_id AND S2.email = S3.email AND S.status = 0
						ORDER BY date ASC";
			
			// perform the query
			$result = mysqli_query($dbc, $query);


			// new submissions
			if (mysqli_num_rows($result) != 0) {
				echo "<h4 align='center'>New submissions</h4>";
			    start_table();
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				    echo build_table(array('s_id'=>$row["s_id"], 'title'=>$row["title"], 'doc_link'=>$row["doc_link"], 's_name'=>$row["s_name"], 's_surname'=>$row["s_surname"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 'status'=>0 ));
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				echo "</div>";
			}

			// formulate the query
			$query =	"SELECT S.s_id, S.title, S.doc_link, S3.s_name, S3.s_surname, S.date,
						S2.p_name 
						FROM submission AS S JOIN submits AS S2 JOIN subscriber AS S3 
						WHERE S.s_id = S2.s_id AND S2.email = S3.email AND S.status = 1
						ORDER BY date ASC";
			
			// perform the query
			$result = mysqli_query($dbc, $query);


			// waiting for feedback
			if (mysqli_num_rows($result) != 0) {
				echo "<h4 align='center'>Waiting for feedback</h4>";
		    	start_table();
	        	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    	echo build_table(array('s_id'=>$row["s_id"], 'title'=>$row["title"], 'doc_link'=>$row["doc_link"], 's_name'=>$row["s_name"], 's_surname'=>$row["s_surname"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 'status'=>1 ));
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				echo "</div>";
			}

			// formulate the query
			$query =	"SELECT S.s_id, S.title, S.doc_link, S3.s_name, S3.s_surname, S.date,
						S2.p_name 
						FROM submission AS S JOIN submits AS S2 JOIN subscriber AS S3 
						WHERE S.s_id = S2.s_id AND S2.email = S3.email AND S.status = 2
						ORDER BY date ASC";
			
			// perform the query
			$result = mysqli_query($dbc, $query);


			// ready for approval
			if (mysqli_num_rows($result) != 0) {
				echo "<h4 align='center'>Ready for approval</h4>";
			    start_table();
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				    echo build_table(array('s_id'=>$row["s_id"], 'title'=>$row["title"], 'doc_link'=>$row["doc_link"], 's_name'=>$row["s_name"], 's_surname'=>$row["s_surname"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 'status'=>2 ));
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				echo "</div>";
			}

			// formulate the query
			$query =	"SELECT S.s_id, S.title, S.doc_link, S3.s_name, S3.s_surname, S.date,
						S2.p_name 
						FROM submission AS S JOIN submits AS S2 JOIN subscriber AS S3 
						WHERE S.s_id = S2.s_id AND S2.email = S3.email AND S.status = 3
						ORDER BY date ASC";
			
			// perform the query
			$result = mysqli_query($dbc, $query);


			// waiting for approval
			if (mysqli_num_rows($result) != 0) {
				echo "<h4 align='center'>Waiting for approval</h4>";
			    start_table();
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				    echo build_table(array('s_id'=>$row["s_id"], 'title'=>$row["title"], 'doc_link'=>$row["doc_link"], 's_name'=>$row["s_name"], 's_surname'=>$row["s_surname"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 'status'=>3 ));
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				echo "</div>";
			}
		}
	?>
</body>
</html>