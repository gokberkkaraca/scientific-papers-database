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

    function signup()
    {
        global $dbc;

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
            if ($role == "reviewer" || $role == "author")
            {
                if(empty($_POST['expertise']))
                {
                    $data_missing[] = 'Expertise';
                }
                else
                {
                    $expertises = $_POST['expertise'];
                }
            }
            else if( $role == "editor" )
            {
                if(empty($_POST['publisher']))
                {
                    $data_missing[] = 'Publisher';
                }
                else
                {
                    $publishers = $_POST['publisher'];
                }
            }
        }
        
        session_start();
        $_SESSION['validationMessage'] = '';

        if(empty($data_missing))
        {
            $checkEmail = "Select count(email) from subscriber where email = '".$email."' ;";
            
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
                
                if ($role == "author")
                {
                    $addAuthor = "insert into subscriber (email,i_name,password,s_name,s_surname,usertype)"
                    ."values ('".$email."', '".$institution."', '".$password."', '".$name."', '".$surname."' , 2);";

                    $stmt = @mysqli_prepare($dbc,$addAuthor) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt) or die(mysqli_error($dbc));


                    $addAuthor = "insert into author (email) values ('".$email."');";
                    $stmt = @mysqli_prepare($dbc,$addAuthor) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt) or die(mysqli_error($dbc));

                    $addExpertise = "insert into authorExpertise (email, tag) values ('".$email."','".$expertises[0]."')";

                    $count = 0;
                    if (is_array($expertises) || is_object($expertises))
                    {
                        foreach ($expertises as $expertise) {
                            if( $count != 0)
                            {
                                $addExpertise .= ", ('".$email."','".$expertise."')";
                            }
                            $count++;
                        }
                    }

                    $addExpertise .= ";";

                    $stmt2 = @mysqli_prepare($dbc,$addExpertise) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt2) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt2) or die(mysqli_error($dbc));


                }
                
                else if ( $role == "reviewer" )
                {
                    $addSub = "insert into subscriber (email,i_name,password,s_name,s_surname,usertype)"
                    ."values ('".$email."', '".$institution."', '".$password."', '".$name."', '".$surname."' , 1);";

                    $stmt = @mysqli_prepare($dbc,$addSub) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt);

                    $addReviewer = "insert into reviewer (email) values ('".$email."');";

                    $stmt = @mysqli_prepare($dbc,$addReviewer) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt);

                    $addExpertise = "insert into reviewerExpertise (email, tag) values ('".$email."','".$expertises[0]."')";

                    $count = 0;
                    if (is_array($expertises) || is_object($expertises))
                    {
                        foreach ($expertises as $expertise) {
                            if( $count != 0)
                            {
                                $addExpertise .= ", ('".$email."','".$expertise."')";
                            }
                            $count++;
                        }
                    }
                    $addExpertise .= ";";

                    $stmt2 = @mysqli_prepare($dbc,$addExpertise) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt2) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt2);
                    
                }
                else if ( $role == "subscriber" )
                {
                    $addSubscriber = "insert into subscriber (email,i_name,password,s_name,s_surname,usertype)"
                    ."values ('".$email."', '".$institution."', '".$password."', '".$name."', '".$surname."' , 0);";

                    $stmt = @mysqli_prepare($dbc,$addSubscriber) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt) or die(mysqli_error($dbc));
                    
                }
                else if ( $role == "editor" )
                {
                    $addEditor = "insert into subscriber (email,i_name,password,s_name,s_surname,usertype)"
                    ."values ('".$email."', '".$institution."', '".$password."', '".$name."', '".$surname."' , 3);";

                    $stmt = @mysqli_prepare($dbc,$addEditor) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt);

                    $addEditor = "insert into editor (email,experience) values ('".$email."', 0);";
                    $stmt = @mysqli_prepare($dbc,$addEditor) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt);

                    $addPublishers = "insert into editorPublisher (email,p_name) values ('".$email."','".$publishers[0]."')";
                    $count = 0;
                    if (is_array($publishers) || is_object($publishers))
                    {
                        foreach ($publishers as $publisher) {
                            if( $count != 0)
                            {
                                $addPublishers .= ", ('".$email."','".$publisher."')";
                            }
                            $count++;
                        }
                    }
                    $addPublishers .= ";";

                    $stmt2 = @mysqli_prepare($dbc,$addPublishers) or die(mysqli_error($dbc));
                    @mysqli_stmt_execute($stmt2) or die(mysqli_error($dbc));
                    @mysqli_stmt_close($stmt2);
                }

                $_SESSION['authenticated'] = 1;
                $_SESSION['validationMessage'] = '';
                $_SESSION['email'] = $email;
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
        signup();
    }


    @mysqli_close($dbc);
?>