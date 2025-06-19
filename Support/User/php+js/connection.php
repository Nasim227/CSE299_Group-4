<?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $database_name = "dreamride";
    $conn = "";

    try{
        $conn = mysqli_connect($server , $username , $password , $database_name);
    }
    catch(mysqli_sql_exception){
        echo"Connection error!!<br>";
    }

?>