<?php
$host="localhost";
$user="testuser";
$password="password";
$database="test";
$dbconnection = mysqli_connect($host,$user,$password,$database);

if(!$dbconnection){
    echo "connection error!";
}
?>