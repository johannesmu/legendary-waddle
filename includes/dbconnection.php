<?php
$host="localhost";
$user=getenv('username');
$password=getenv('dbpassword');
$database="test";
$dbconnection = mysqli_connect($host,$user,$password,$database);

if(!$dbconnection){
    echo "connection error!";
}
?>