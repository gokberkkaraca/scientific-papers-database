<?php
	include("config.php");
	session_start();
	if (isset($_SESSION['email'])) {
		$user_type = $_SESSION['type'];
		if ($user_type != 3) {
			header('Location: main.php');
		}
	}
 ?>
<html>
<head>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/author-submissions.css">
	<link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700" rel="stylesheet">
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
	<script src="js/editor-submission.js"></script>

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
	<script>


		function invite_reviewer(s_id, status)
		{
			// Adding structure HTML
			$(".added_div").remove();
			$(".heading").text("Invite Reviewer");



			$(".popup-content").append( '<div class="added_div" data-sid="'+ s_id +'" data-status="'+ status +'"><div class="col-6 invite_input_div">'+
			'<div class="form-group"><input class="form-control invite_input" type="text" name="reviewer_name" placeholder="Enter a name"></div>'+
			'<div class="form-group"><select class="form-control expertise_select invite_input" name="expertise"></select></div>'+
			//'<button class="col-3 btn btn-primary" onclick="closePopup();">OK</button>
			'</div>'+
			'<div class="matching_reviewers"></div></div>');

			// Filling the expertises select
			$.ajax({
				type: "POST",
				url: "functions.php?getExpertises=true",
				dataType: "json",
				success: function(response){

					var arr = ("" + response).split(",");

					if(arr.length > 0 )
					{
						$expertSel = $(".expertise_select");
						var count = 1;
						arr.forEach(element => {
							$expertSel.append('<option value="'+ element +'">'+ element +'</option>');
						});
					}
				}
			});

			loadReviewersJson(s_id, status);

			// Adding event handlers to input and select
			$(".invite_input").on("change", function(){
				loadReviewersJson(s_id, status);
			});

/*
			$("input.invite_input").on("onkeypress,onkeyup", function(){
				loadReviewersJson();
			});
*/

			// Displaying
			$(".popup_div")[0].style.display = "block";

		}

		function loadReviewersJson(s_idArg, statusArg)
		{
			$( ".matching_reviewers" ).empty();

			var revName = $("input.invite_input").val();
			var selExpertise = $("select.invite_input > option:selected").val();


			$.ajax({
				type: "GET",
				url: "functions.php?loadReviewers=true",
				data: {name:revName, expertise:selExpertise},
				async: false,
				dataType: "json",
				success: function(response){

//						alert(response.reviewers[0].name);

					var arr = Object.values(response.reviewers)

					if(arr.length <= 0 )
					{
						alert("No reviewer with these info exists!");
					}
					else
					{

						var toAdd = '<table class="table table-striped"><tbody>';
						arr.forEach(element => {

							toAdd +=  '<tr><td><div><label>'+ element.name +'</label><div><ul>';

							element.expertises.forEach( val => {

								toAdd += '<li>'+ val +'</li>';
							});

							toAdd += '</ul></div</div></td><td>'+
							'<button id="'+ element.email +'" class="invite_btn btn btn-primary">Invite</button>'+
							'</td></tr>';

						});
						toAdd += '</tbody></table>';
						$(".matching_reviewers").append( toAdd );
					}
				}
			});
			$("button.invite_btn").on("click", function(){

				//var eventData = event.data;
				var revEmail = $(this).attr("id");
				var s_id = s_idArg;
				var status = statusArg;
				//var s_id = $(".addedDiv").data("sid");
				//var status = $(".addedDiv").data("status");

				//attr('data-fruit')

				alert( revEmail +  " " +  s_id +" " + status);

				$.ajax({
						type: "GET",
						url: "functions.php?inviteRev=true",
						data: {email:revEmail, id:s_id, stat:status},
						dataType: "text",
						success: function(response){
							alert(response);
							$( "#" + $.escapeSelector(revEmail) ).removeClass("btn-primary").addClass("btn-success").text('invited').prop('disabled',true);

						}
				});

			});
		}


		function see_reviewers(s_id) {

			$(".added_div").remove();
			$(".heading").text("Reviewers");

			$.ajax({
				type: "GET",
				url: "functions.php?getReviewers=true",
				data: {id:s_id},
				dataType: "json",
				success: function(response){
					var arr = ("" + response).split(",");

					if(arr.length > 0 )
					{
						$(".popup-content").append( '<div class="added_div">' +
						'<table class="table table-striped"><thead><tr><th>Name</th><th>Surname</th><th>Email</th><th>Institution</th></tr></thead>'+
						'<tbody class="added_tbody"></tbody></table></div>');
						$table = $(".added_tbody");
						for ( var i = 0; i < arr.length; i = i + 4 )
						{
							$table.append('<tr><td>'+ arr[i] +'</td><td>'+ arr[i+1] +'</td><td>'+ arr[i+2] +'</td><td>'+ arr[i+3] +'</td></tr>');
						}
					}
				}
			});

			$(".popup_div")[0].style.display = "block";
		}

		function see_feedback(s_id) {


			$(".added_div").remove();
			$(".heading").text("Feedback");

			$.ajax({
				type: "GET",
				url: "functions.php?getFeedbackEditor=true",
				data: {id:s_id},
				dataType: "json",
				success: function(response){
					var arr = ("" + response).split(",");

					if(arr.length > 0 )
					{
						$(".popup-content").append( '<div class="added_div">' +
						'<table class="table table-striped"><thead><tr><th>Full Name</th><th>Feedback</th></tr></thead>'+
						'<tbody class="added_tbody"></tbody></table></div>');
						$table = $(".added_tbody");
						for ( var i = 0; i < arr.length; i = i + 2 )
						{
							$table.append('<tr><td>'+ arr[i] +'</td><td>'+ arr[i+1] +'</td></tr>');
						}
					}
				}
			});

			$(".popup_div")[0].style.display = "block";
		}

		function send_for_approval(s_id) {


			$.ajax({
				type: "GET",
				url: "functions.php?sendBackToAuthor=true",
				data: {id:s_id},
				success: function(response){
					location.reload();
				}
			});
		}


		window.onclick = function(event)
		{
			var popup_div = $(".popup_div")[0];
			if (event.target == popup_div)
			{
				popup_div.style.display = "none";
			}
		}

		function closePopup()
		{
			$(".popup_div")[0].style.display = "none";
		}

		function reject(s_id)
		{

			$.ajax({
				type: "GET",
				url: "functions.php?reject=true",
				data: {id:s_id},
				success: function(response){
					location.reload();
				}
			});
		}


	</script>
	<div class="popup_div">

		<div class="popup-content">
			<span class="close" onclick="closePopup();">&times;</span>
			<h3 class="heading"></h3>
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
						. "<button class=\"btn btn-danger\" style=\"margin-left: 10px\" onclick=" . "reject('$array[s_id]');"
	    				. ">Reject</button>";
	    				//. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>";
	    	}

	    	// waiting for feedback -> see reviewers
	    	else if ($array['status'] == 1) {
	    		$html .= '<td>'
	    				. '<button class="btn btn-info" onclick="' . "see_reviewers('$array[s_id]');"
						. '">See Reviewers</button>'
						. "<button class=\"btn btn-danger\" style=\"margin-left: 10px\" onclick=" . "reject('$array[s_id]');"
	    				. ">Reject</button>";
	    				//. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>";
	    	}

	    	// ready for approval -> send for approval
	    	else if ($array['status'] == 2) {
	    		$html .= '<td>'
	    				. '<button class="btn btn-info" onclick="' . "see_feedback('$array[s_id]');"
						. '">See feedback</button>'
						. "<button class=\"btn btn-danger\" style=\"margin-left: 10px\" onclick=" . "reject('$array[s_id]');"
	    				. ">Reject</button>"
	    				. '<p></p>'
	    				. '<button class="btn btn-info" onclick="' . "send_for_approval('$array[s_id]');"
						. '">Send for approval</button>';
						//. "<a href=reviewer-submission.php?reject=true&s_id=$array[s_id]&editor_email=$email><button class=\"btn btn-danger\" style=\"margin-left: 10px\">Reject</button></a>"
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

			$is_empty = 0;

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
				$is_empty = 1;	
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
				$is_empty = 1;
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
				$is_empty = 1;
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
				$is_empty = 1;
			}

			if ($is_empty == 0) {
				echo "<h4 align='center'>There are no submissions assigned to you</h4>";
			}
		}
	?>
</body>
</html>
