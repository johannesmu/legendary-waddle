<?php
session_start();
//if cart does not exist, create cart session variable
//the session cart is redundant, will be removed
if(!$_SESSION["cart"]){
    $_SESSION["cart"]=array();
}
//user is not logged in create a temporary userid
if(!$_SESSION["id"]){
    $_SESSION["id"] = session_id();
}
function generateDateTime(){
    $date = new DateTime("now", new DateTimeZone('Australia/Sydney') );
    $date = $date->format("Y-m-d H:i:s");
    return $date;
}

function generateToken(){
    $seed = openssl_random_pseudo_bytes(16);
    $token = bin2hex($seed);
    return $token;
}
?>