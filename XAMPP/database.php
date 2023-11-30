<?php
$host_name = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "login-register";

$conn_bool = mysqli_connect($host_name, $db_user, $db_password, $db_name);

if(!$conn_bool){
    die("Something went wrong.");
}

?>