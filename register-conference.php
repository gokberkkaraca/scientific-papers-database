<?php

    require_once('config.php');

    function register()
    {
        global $dbc;

        $p_name = $_GET['conf'];
        $name = $_GET['name'];
        $surname = $_GET['surname'];

        $checkExists = "select count(*) from audience where p_name = '".$p_name."' and a_name = '".$name."' and a_surname = '".$surname."';";
        
        $stmt = @mysqli_query($dbc,$checkExists);
        $count = @mysqli_fetch_array($stmt);

        if ( intval( $count['count(*)'] ) > 0 )
        {
            return '{ "result":0 }';
        }
        else
        {
            //$addConfMember = "insert into audience (p_name, a_name,a_surname) values ('".$p_name."', '".$name."', '".$surname."');";
            //$stmt = @mysqli_query($dbc,$addConfMember);
            return '{ "result":1 }';
        }
        

        @mysqli_stmt_close($stmt);
    }

    if(isset($_GET['register']))
    {
            $res = register();
            echo $res;
    }
    
    @mysqli_close($dbc);
?>