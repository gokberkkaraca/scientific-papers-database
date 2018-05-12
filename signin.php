<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/signup.css" />
</head>

<?php
    if (isset($_GET['error'])) {
      echo "<script type='text/javascript'>alert('No such user exists')</script>";
    }
?>

<body>
    <div class="container" align="center">
    <div class="scilib_theader col-4">
        <h3>Scilib</h3>
    </div>
    <div class="signup_div col-4">
        <form method="post" action="functions.php">
            <div class="form-group">
                <input type="text" class="form-control" name="email" placeholder="E-mail" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="submit_signin" class="form-control btn btn-primary">Sign In</button>
            </div>
            <a href="signup.php">Sign Up</a>
        </form>
    </div>
</div>
</body>
</html>
