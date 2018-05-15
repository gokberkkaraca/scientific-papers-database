<?php
require('config.php');
$sql = "SELECT p_name, conference_topic, date FROM conference WHERE date > CURRENT_DATE() ORDER BY date ASC";
$result = mysqli_query($dbc,$sql);

if($_SERVER["REQUEST_METHOD"] == "POST") {
 $name = mysqli_real_escape_string($dbc,$_POST['name']);
 $surname = mysqli_real_escape_string($dbc,$_POST['surname']);
 $p_name = $_POST['conference'];
 $sql = "INSERT INTO audience VALUES ('$p_name Conferences', '$name', '$surname')";

if( mysqli_query($dbc, $sql) ){
  echo '<script language="javascript">';
  echo 'alert("Registered successfully!")';
  echo '</script>';
}
else
{
  echo '<script language="javascript">';
  echo 'alert("Audience already exists!")';
  echo '</script>';
} 
}
?>
<html>
<head>
  <meta charset="utf-8">
  <title>Find Conference</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  <link rel="stylesheet" href="css/find-conference.css" />
  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
</head>
<body>
  <div class="container" align="center" style="margin-top: 30px">
    <div class="scilib_theader col-4">
      <h3>Find Conference</h3>
    </div>
    <div class="col-4">
      <form method="POST">
        <div class="form-group">
          <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" required>
        </div>
        <div class="form-group">
          <label for="role">Select a conference</label>
          <select id="conferences_select" class="form-control" name="conference" required>
            <?php
            if(isset($result)){
              while ( $row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                echo '<option value='.$row[0].' title='.$row[1].'>'.$row[0].' ('.$row[1].')</option>';
              }
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <button type="submit" name="register" href="#" id="register" class="form-control btn btn-primary">Register</button>
        </div>
      </form>
      <div>
        <a href="index.php"><button type="submit" name="submit_signup" class="form-control btn btn-info">Go Home</button></a>
      </div>
    </div>
  </body>
  </html>
