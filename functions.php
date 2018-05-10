<?php

    require_once('config.php');

    function getPublishersJson() 
    {
        global $dbc;

        $selPublishers = "select p_name from publisher;";

        $stmt = @mysqli_query($dbc,$selPublishers);
        
        $publishers = array();

        while($row = @mysqli_fetch_array($stmt))
        {
            array_push($publishers, $row['p_name']);
            //$publishers[] = $row['p_name'];
        }

        //$arr = array($publishers[]);
        $jsonRes = json_encode($publishers);

        @mysqli_stmt_close($stmt);

        return $jsonRes;
    }

    function getExpertisesJson() 
    {
        global $dbc;

        $selExpertises = "select tag from expertise;";

        $stmt = @mysqli_query($dbc,$selExpertises);
        
        $expertises = array();

        while($row = @mysqli_fetch_array($stmt))
        {
            array_push($expertises, $row['tag']);
            //$publishers[] = $row['p_name'];
        }

        //$arr = array($publishers[]);
        $jsonRes = json_encode($expertises);

        @mysqli_stmt_close($stmt);

        return $jsonRes;
    }

    if (isset($_GET['getPublishers'])) 
    {
        $res = getPublishersJson();
        echo $res;
        //getPublishersJson($_GET['closeID']);
    }
    if (isset($_GET['getExpertises'])) 
    {
        $res = getExpertisesJson();
        echo $res;
        //getExpertisesJson($_GET['closeID']);
    }

    if(isset($_POST['submit_signup']))
    {

    $data_missing = array();

    if(empty($_POST['name']))
    {
        $data_missing[] = 'Name';
    }
    else
    {
        $name = $_POST['name'];
    }
    if(empty($_POST['surname']))
    {
        $data_missing[] = 'Surname';
    }
    else
    {
        $surname = $_POST['surname'];
    }
    if(empty($_POST['email']))
    {
        $data_missing[] = 'Email';
    }
    else
    {
        $email = $_POST['email'];
    }
    if(empty($_POST['password']))
    {
        $data_missing[] = 'Password';
    }
    else
    {
        $password = $_POST['password'];
    }
    if(empty($_POST['institution']))
    {
        $data_missing[] = 'Institution';
    }
    else
    {
        $institution = $_POST['institution'];
    }
    if(empty($_POST['role']))
    {
        $data_missing[] = 'Role';
    }
    else
    {
        $role = $_POST['role'];
        if ($role == "Reviewer" || $role == "Author")
        {
            if(empty($_POST['expertise']))
            {
                $data_missing[] = 'Expertise';
            }
            else
            {
                $expertise = $_POST['expertise'];
            }
        }
        else if( $role == "Editor" )
        {
            if(empty($_POST['publisher']))
            {
                $data_missing[] = 'Publisher';
            }
            else
            {
                // Get publishers array
                //$expertise = $_POST['expertise'];
            }
        }
    }

    session_start();
    $_SESSION['validationMessage'] = '';

    if(empty($data_missing))
    {
        $checkEmail = "Select count(email) from subscriber, reviewer, author, editor where email = '".$email."' ;";
        
        $stmt = @mysqli_query($dbc,$checkEmail);

        $count = @mysqli_fetch_array($stmt);

        if( intval($count['count(email)']) > 0 )
        {
            // Email already exists
            $_SESSION['validationMessage'] = 'This E-mail is already used';
            header('Location: signup.php');
            exit();
        }
        else
        {
            // Create account



            $_SESSION['authenticated'] = 1;
            $_SESSION['validationMessage'] = '';
            header('Location: main.php');
            exit();
        }
        
        @mysqli_stmt_close($stmt);
    }
    else
    {
        $_SESSION['validationMessage'] = 'Some fields are missing!';
    }
}

    @mysqli_close($dbc);
?>