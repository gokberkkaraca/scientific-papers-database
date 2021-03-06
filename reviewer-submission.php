<?php
	include("config.php");
	session_start();
	if (isset($_SESSION['email'])) {
		$user_type = $_SESSION['type'];

		if ($user_type != 1) {
			header('Location: main.php');
		}
	}
 ?>
<html>
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<link rel="stylesheet" href="css/main.css">
	<link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700" rel="stylesheet">
<script type="text/javascript">
	function writeFeedback(s_id, email) {
    	popupWindow = window.open('write-reviewer-feedback.php?s_id=' + s_id + '&editor_email=' + email,'popUpWindow','height=300,width=400,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');
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
	<div class="container-fluid">
		  <div class="jumbotron">
				<form name="reviewer-submission" id="reviewer-submission-form">

					<div class="row">
						<div class="col-md-12"><h1 id="scilib-title">Waiting for review</h1></div>
					</div>
				</form>
			</div>
	</div>
</div>
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
    	$html .= '<td>' . htmlspecialchars($array['date']) . '</td>';
    	$html .= '<td>' . htmlspecialchars($array['p_name']) . '</td>';
      	$html .= '<td>' . '<button class="btn btn-info" onclick="' . "writeFeedback('$array[s_id]', '$array[email]');" . '">Write Feedback</button>' . "<a href=reviewer-submission.php?reject=true&reviewer_email=$email&s_id=$array[s_id]&editor_email=$array[email]><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>";
      	$html .= '</tr>';

	    return $html;
	}

	if (isset($_SESSION["email"])) {
		$email = $_SESSION["email"];
  		$user_type = $_SESSION["type"];

		// formulate the query
		$query = 	"SELECT S.title, S.doc_link, S.date, S2.p_name, I.editor_email, S.s_id
					FROM invites AS I JOIN submission AS S JOIN submits as S2
					WHERE I.s_id = S.s_id
					AND S.s_id = S2.s_id
					AND I.reviewer_email = \"$email\"
					ORDER BY date ASC";
		// perform the query
		$result = mysqli_query($dbc,$query);

		if (mysqli_num_rows($result) != 0) {
		    echo "<div id=\"result-panel\" align=\"center\">";
			echo "<div align=\"center\">";
			echo "<table class=\"table table-striped\">";
			echo "<thead class=\"thead-light\">";
			echo "<tr align='center'>";
			echo "<th scope=\"col\">Title</th>";
			echo "<th scope=\"col\">Date</th>";
			echo "<th scope=\"col\">Publisher Name</th>";
			echo "<th scope=\"col\">Action</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    echo build_table(array('title'=>$row["title"], 'doc_link'=>$row["doc_link"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 'email'=>$row["editor_email"], 's_id'=>$row["s_id"]));
			}
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
			echo "</div>";
		}
		else {
			echo "<h4 align='center'>There are no submissions assigned to you</h4>";
		}
	}
?>
</body>
</html>
