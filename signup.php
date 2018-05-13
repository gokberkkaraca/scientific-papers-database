<!DOCTYPE html>
<html>
    <head>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
            <link rel="stylesheet" href="css/signup.css" />
            <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
            <script src="js/signup.js"></script>
    </head>
    <body>
        <div class="container" align="center">
            <div class="scilib_theader col-4">
                    <h3>Scilib</h3>
            </div>
            <div class="signup_div col-4">
                <form method="post" action="functions.php">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="surname" placeholder="Surname" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>

<?php

require_once('config.php');

getInstitutions();

function getInstitutions() 
{
    global $dbc;

    $selInstitutions = "select i_name from institution;";

    $stmt = @mysqli_query($dbc,$selInstitutions);

    echo '<div class="form-group"><label for="institution">Select your institution</label>'
    .'<select class="form-control" name="institution" required>';
    while($row = @mysqli_fetch_array($stmt))
    {
        echo '<option value="'.$row['i_name'].'">'.$row['i_name'].'</option>';
    }
    echo '</select></div>';

    @mysqli_stmt_close($stmt);

}

@mysqli_close($dbc);

?>

                    <div class="form-group role_select_div">
                            <label for="role">Select account type (your role)</label>
                            <select id="signup_role_select" class="form-control" name="role" required>
                                <option value="subscriber">Subscriber</option>
                                <option value="reviewer">Reviewer</option>
                                <option value="author">Author</option>
                                <option value="editor">Editor</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit_signup" class="form-control btn btn-primary">Sign Up</button>
                    </div>
                    <a href="signin.html">Sign In</a>
                </form>
            </div>
        </div>
    </body>
</html>