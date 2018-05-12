<!DOCTYPE html>
<?php
  require('config.php');
  session_start();
  if(isset($_GET['s_id']) && isset($_GET['editor_email'])) {
    $s_id = $_GET['s_id'];
    $editor_email = $_GET['editor_email'];
    $reviewer_email = $_SESSION['email'];
  }
  else {
    header("location:index.php");
  }
 ?>

<html>
  <head>
    <meta charset="utf-8">
    <title>Write Feedback</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  </head>
  <body>
  <form method="POST" action="">
    <div class="form-group" align="center">
      <label for="comment">Feedback:</label>
      <textarea class="form-control" rows="5" id="comment" name="feedback" required></textarea>
      <br />
      <button type="submit" class="btn btn-success" name="save">Save</button>
      <?php
        if (isset($_POST['save'])) {
          if (isset($_POST['feedback'])) {
            $feedback = $_POST['feedback'];
            $sql = "INSERT INTO reviews VALUES(\"$reviewer_email\", \"$editor_email\", \"$s_id\",  \"$feedback\")";
            mysqli_query($dbc, $sql);

            $sql = "DELETE FROM invites WHERE reviewer_email='$reviewer_email' AND editor_email='$editor_email' AND s_id='$s_id'";
            mysqli_query($dbc, $sql);

            echo "<script>window.close();</script>";
          }
        }
      ?>
    </div>
  </form>
  </body>
</html>
