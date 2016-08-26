<?php
include("includes/session.php");
include("includes/dbconnection.php");

//redirect to originating page ie page where add to cart button was clicked when done
$redirect = ($_SERVER[HTTP_REFERER]);
//prevent multiple of the same variable
if(strpos($redirect,'&success=true')){
  $redirect = str_replace('&success=true','',$redirect);
}
if(strpos($redirect,'&success=')){
  $redirect = str_replace('&success=true','',$redirect);
}

//-------------Shopping Cart-------------
//because the form contains two submit buttons, if button with value "cart" is clicked
if($_POST["submit"]=="cart"){
    //receive and sanitise variables from POST request
    $productid = filter_var($_POST["productid"],FILTER_SANITIZE_STRING);
    $quantity = filter_var($_POST["quantity"],FILTER_SANITIZE_NUMBER_INT);
    //generate time
    $time = generateDateTime();
    //get user id from session
    $userid = $_SESSION["id"];
    //set status to 0 since the item has not been checked out
    $status = 0;
    
    //assume item is not in already in cart
    $itemincart = false;
    //count items in session cart
    $cartcount = count($_SESSION["cart"]);
    //if there are items in the cart
    if($cartcount>0){
      //loop through all items in cart to see if product is already in cart
      for($i=0;$i<$cartcount;$i++){
        $cartitemid = $_SESSION["cart"][$i]["productid"];
        $cartquantity = $_SESSION["cart"][$i]["quantity"];
        //if cart is the same as item being added
        if($cartitemid==$productid){
          //set itemincart to true so further down it does not get re added to cart
          $itemincart=true;
          //update the quantity of item in cart
          $newquantity = $cartquantity + $quantity;
          //update the cart item quantity in database
          $query = "UPDATE cart SET quantity='$newquantity' WHERE productid='$cartitemid'";
          if($dbconnection->query($query)){
            //if update to database is successful update the cart session
            $_SESSION["cart"][$i]["quantity"] = $newquantity;
            //set success to true
            $success=true;
          }
          else{
            //if database update failed set success to false
            $success=false;
          }
        }
      }
    }
    //if item does not exist in cart
    if($itemincart==false){
      //add it to database
      echo "<p>user:$userid product:$productid quantity:$quantity status:$status time:$time</p>";
      $query = 
      "INSERT INTO cart (userid,productid,quantity,status,time) 
      VALUES ('$userid','$productid','$quantity','0','$time')";
      //if adding to database succeeds
      if($dbconnection->query($query)){
        //add it to session cart
        array_push($_SESSION["cart"],array("productid"=>$productid,"quantity"=>$quantity));
        $success=true;
      }
      else{
        $success=false;
      }
    }
    echo $success;
    $url ="$redirect"."&success=$success";
    // redirect back to originating page
    header("location:$url");
}


//-------if the wishlist button has been clicked
elseif($_POST["submit"]=="wish"){
  //add to the wishlist
  //sanitise the product id
  $productid = filter_var($_POST["productid"],FILTER_SANITIZE_NUMBER_INT);
  //generate date time for "now"
  $time = generateDateTime();
  //get user id
  $userid = $_SESSION["id"];
  echo "<p>user: $userid product: $productid time: $time </p>";
  //check if there is a wishlist session variable, if not create one
  if(!$_SESSION["wishlist"]){
    $_SESSION["wishlist"]=array();
  }
  //check if the item already exists in wishlist
  //count items in wishlist
  $wishcount = count($_SESSION["wishlist"]);
  //if there are items in wishlist
  if($wishcount > 0){
    $itemexists = false;
    //loop through all items in wishlist
    for($i=0;$i<$wishcount;$i++){
      $wishid = $_SESSION["wishlist"][$i];
      if($productid==$wishid){
        $itemexists = true;
      }
    }
    //if item is not in wishlist
    if(!$itemexists){
      //add to database
      $query = "INSERT INTO wishlist (productid,time,userid) VALUES ('$productid','$time','$userid')";
      if($dbconnection->query($query)){
        array_push($_SESSION["wishlist"],$productid);
        $success=true;
      }
      else{
        $success=false;
      }
    }
  }
  else{
    //add to database
    $query = "INSERT INTO wishlist (productid,time,userid) VALUES ('$productid','$time','$userid')";
    if($dbconnection->query($query)){
      array_push($_SESSION["wishlist"],$productid);
      $success=true;
    }
    else{
      $success=false;
    }
  }
  $url ="$redirect"."&success=$success";
  header("location:$url");
}


?>