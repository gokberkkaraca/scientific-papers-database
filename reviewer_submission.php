<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
<script type="text/javascript">
	function writeFeedback(s_id) {
		alert('This is popup');
	}
</script>
</head>
<body>
<?php 
		include("config.php");

		function build_table($array){
			$i = 0;

	    	$html = '<tr>';

	        foreach($array as $key=>$value){
	        	if ($key != 's_id'){
	            	$html .= '<td>' . htmlspecialchars($value) . '</td>';
	        	} 
	        }
	        $html .= '<td>' . '<button onclick="' . "writeFeedback($value);" . '">Write Feedback</button>' . '</td>';

		    return $html;
		}

		session_start();
		if (isset($_SESSION["email"])) {
			$email = $_SESSION["email"];

			// formulate the query
			$query = 	"SELECT * FROM invites AS I JOIN submission AS S JOIN submits as S2
						WHERE I.s_id = S.s_id
						AND S.s_id = S2.s_id
						AND S.email = S2.email;";

			// perform the query
			$result = mysqli_query($dbc,$query);

			// check number of rows to see if table is empty
	        $num_rows = mysqli_num_rows($result);

	        if ($num_rows > 0) {
	        	echo "<b>Waiting for review:<b>";
	    	} else {
	    		echo "<b>This place is empty<b>";
	    	}

	    	echo '<table style="width:80%">';
	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			    echo build_table(array('title'=>$row["title"], 'doc_link'=>$row["doc_link"], 'date'=>$row["date"], 'p_name'=>$row["p_name"], 's_id'=>$row["s_id"]));
			}	 
			echo '</table>';
		}
	?>
</body>
</html>
