<!DOCTYPE html>
<html>
    <head>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
            <link rel="stylesheet" href="css/author_submissions.css" />
            <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
            <script src="js/author_submissions.js"></script>
    </head>
    <body>
        <div class="container" align="center">
            <div class="author_submissions_div">
                    
            <div class="feedback_div">

                <div class="feedback-content">
                    <span class="close">&times;</span>
                    <h3>Feedback</h3>
                </div>

            </div>

                    <?php

                    require_once('config.php');
                    session_start();
                    $email = 'email2';
                    //$email = $_SESSION['email'];

                    getAuthorSubmissions();

                    function getAuthorSubmissions() 
                    {
                        global $dbc;
                        global $email;

                        $selAuthSubmissions = "select submission.s_id as s_id, title, p_name, status from submission, submits
                        where submission.s_id = submits.s_id
                        and submits.email = '".$email."' order by date DESC;";

                        $stmt = @mysqli_query($dbc,$selAuthSubmissions) or die(mysqli_error($dbc));

                        echo '<table class="table table-striped table-bordered">
                        <thead align="center"><tr><th>Title</th><th>Publisher</th><th>Status</th><th>Feedback</th><th colspan="2">Options</th></tr></thead><tbody align="center">';

                        while($row = @mysqli_fetch_array($stmt))
                        {
                            $publishBtn = 'disabled';
                            $cancelBtn = 'disabled';
                            $feedbackBtn = 'disabled';
                            $statusStr = '';

                            switch( $row['status'])
                            {
                                case 0:
                                {
                                    $statusStr = 'Submitted';
                                    $cancelBtn = '';
                                }
                                break;
                                case 1:
                                {
                                    $statusStr = 'Under reviewing';
                                    $cancelBtn = '';
                                }
                                break;
                                case 2:
                                {
                                    $statusStr = 'Under editor last check';
                                    $cancelBtn = '';
                                }
                                break;
                                case 3:
                                {
                                    $statusStr = 'Waiting approval from author(s)';
                                    $cancelBtn = '';
                                    $feedbackBtn = '';
                                    $publishBtn = '';
                                }
                                break;
                                case 4:
                                {
                                    $statusStr = 'Sent for publication';
                                    $cancelBtn = '';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                                case 5:
                                {
                                    $statusStr = 'Published';
                                    $cancelBtn = 'disabled';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                                case 6:
                                {
                                    $statusStr = 'Rejected';
                                    $cancelBtn = 'disabled';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                            }

                            echo '<tr><td><a id="'.$row['s_id'].'" href="publication-page.php?id='.$row['s_id'].'">'.$row['title'].'</a></td>
                                    <td>'.$row['p_name'].'</td>
                                    <td>'.$statusStr.'</td>
                                    <td><button class="see_feedback_btn" '.$feedbackBtn.'>See feedback</button></td>
                                    <td><button class="publish_btn" '.$publishBtn.'>Publish</button></td>
                                    <td><button class="cancel_btn" '.$cancelBtn.'>Cancel</button></td></tr>';
                        }
                        echo '</tbody></table>';

                        @mysqli_stmt_close($stmt);

                    }

                    //@mysqli_close($dbc);
                    //onclick="functions.php?cancel=true&s_id='.$row['s_id'].'"

                    ?>
            </div>
            <div class="make_nsubmission_div col-6">
                <form method="post" action="functions.php">
                    <div class="form-group row">
                        <input type="text" class="form-control" name="nsubmission_title" placeholder="Title" required>
                    </div>
                    <div class="form-group row">
                        <input type="text" class="form-control col-6" name="nsubmission_link" placeholder="Google Doc Link" required>
                        
                        <?php

                            //require_once('config.php');
                            getPublishers();

                            function getPublishers() 
                            {
                                global $dbc;
                            
                                $selPublishers = "select p_name from publisher;";
                            
                                $stmt = @mysqli_query($dbc,$selPublishers) or die(mysqli_error($dbc));
                            
                                echo '<select class="form-control col-4 offset-2" name="nsubmission_publisher" placeholder="Publisher" required>';
                                while($row = @mysqli_fetch_array($stmt))
                                {
                                    echo '<option value="'.$row['p_name'].'">'.$row['p_name'].'</option>';
                                }
                                echo '</select>';
                            
                                @mysqli_stmt_close($stmt);
                            
                            }
                        ?>

                        
                    </div>
                    <div class="form-group row">
                        <input type="email" class="form-control" name="coauthors_emails" placeholder="Co-Authors emails seperated by comma">
                    </div>
                    <div class="form-group row">
                        <label for="expertises" style="display: block; text-align: left;">Field(s) of expertise</label>

                        <?php

                        //require_once('config.php');
                        getExpertises();

                        function getExpertises() 
                        {
                            global $dbc;

                            $selExpertises = "select tag from expertise;";

                            $stmt = @mysqli_query($dbc,$selExpertises) or die(mysqli_error($dbc));

                            echo '<select class="form-control" name="expertises[]" placeholder="Expertise" required multiple>';
                            while($row = @mysqli_fetch_array($stmt))
                            {
                                echo '<option value="'.$row['tag'].'">'.$row['tag'].'</option>';
                            }
                            echo '</select>';

                            @mysqli_stmt_close($stmt);

                        }
                        ?>

                    </div>
                    <div class="form-group">
                        <button type="submit" name="make_a_submission" class="form-control btn btn-primary">Make a submission</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>