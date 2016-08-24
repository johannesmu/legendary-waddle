<?php
include("includes/session.php");
include("includes/dbconnection.php");
//establish user id
$userid = $_SESSION["id"];

//----------delete cart item
//if user has clicked on delete button
if($_POST["submit"]=="delete"){
  //sanitise the id
  $cartitemid = filter_var($_POST["id"],FILTER_SANITIZE_NUMBER_INT);
  $productid = filter_var($_POST["productid"],FILTER_SANITIZE_NUMBER_INT);
  
  $query = "DELETE FROM cart WHERE id='$cartitemid'";
  if($dbconnection->query($query)){
    //update session cart
    $cartcount = count($_SESSION["cart"]);
    for($i=0;$i<$cartcount;$i++){
      $cartitem = $_SESSION["cart"][i]["productid"];
      if($cartitem==$productid){
        array_splice($_SESSION["cart"][$i],1);
      }
    }
    $success = true;
    $message = "Item removed from cart";
  }
  else{
    $success = false;
    $message = "A problem occured";
  }
}
//----------update cart item
//if user has clicked on update button
if(isset($_POST["update"])){
  //sanitise the id
  $cartitemid = filter_var($_POST["id"],FILTER_SANITIZE_NUMBER_INT);
  //sanitise the quantity
  $quantity = filter_var($_POST["quantity"],FILTER_SANITIZE_NUMBER_INT);
  $query = "UPDATE cart SET quantity='$quantity' WHERE id='$id'";
  if($dbconnection->query($query)){
    $success = true;
    $message = "Item updated";
  }
  else{
    $success = false;
    $message = "A problem occured";
  }
}

//----------display cart items
//create array to store cart contents
$cartarray = array();
//get cart contents from the database by joining products and cart tables
//so we can get images and description
$query = "SELECT 
          cart.id AS id,
          cart.productid AS productid,
          cart.quantity AS quantity,
          products.image AS image,
          products.name AS name,
          products.description AS description,
          products.price AS price
          FROM cart 
          INNER JOIN products 
          ON products.id = cart.productid
          WHERE cart.userid = '$userid'
          AND cart.status='0'";
$result = $dbconnection->query($query);
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    array_push($cartarray,$row);
  }
}
?>
<!doctype html>
<html>
  <?php include("includes/head.php");?>
  <body>
    <?php include("includes/navigation.php");?>
    <div class="container">
      <div class="row">
        <h3 class="col-md-12">Cart Contents</h3>
      </div>
      <?php
      if(count($cartarray)==0){
        echo 
        "<div class=\"site-alert alert alert-warning alert-dismissible text-center\" role=\"alert\">
          <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
            <span aria-hidden=\"true\">&times;</span>
          </button>
          Whoops! Your cart is empty, <a href=\"index.php\">Let's go shopping!</a>
        </div>";
      }
      $totalitems = 0;
      $totalprice = 0;
      foreach($cartarray as $cartitem){
        $id = $cartitem["id"];
        $name = $cartitem["name"];
        $price = $cartitem["price"];
        $totalprice += $price;
        $quantity = $cartitem["quantity"];
        $totalitems += $quantity;
        $productid = $cartitem["productid"];
        $image = $cartitem["image"];
        $description = $cartitem["description"];
        //output a row with item data
        echo 
        "<div class=\"row cart-product\">
          <div class=\"col-md-2\">
            <img class=\"img-responsive\" src=\"images/$image\">
          </div>
          <div class=\"col-md-4\">
            <h4>$name</h4>
            <p class=\"price\">$price</p>
            <p>$description</p>
            <p><a href=\"detail.php?id=$productid\">Product detail</a></p>
          </div>
          <div class=\"col-md-6 text-right\">
            <form class=\"form-inline\" action=\"shopping-cart.php\" method=\"post\">
              <input type=\"hidden\" name=\"id\" value=\"$id\">
              <input type=\"hidden\" name=\"productid\" value=\"$productid\">
              <label for=\"item$id\">Quantity</label>
              <input type=\"number\" id=\"item$id\" class=\"form-control quantity\" value=\"$quantity\">
              <button class=\"btn btn-default\" name=\"submit\" value=\"update\" type=\"submit\">Update</button>
              <button class=\"btn btn-default\" name=\"submit\" value=\"delete\" type=\"submit\">&times;</button>
            </form>
          </div>
        </div>
        <hr>";
      }
      //output the totals
        echo 
        "<div class=\"row\">
          <div class=\"col-md-6 col-md-offset-6 text-right\">
            total items: $totalitems
            &nbsp;
            total price: <span class=\"price\">$totalprice</span>
          </div>
        </div>";
      ?>
    </div>
    <?php
    $showalert = false;
    if($success==true){
      $class="alert-success";
      $showalert="true";
    }
    elseif($success===false){
      $class="alert-warning";
      $showalert="true";
    }
    if($showalert){
    echo 
    "<div class=\"site-alert alert $class alert-dismissible text-center\" role=\"alert\">
      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        <span aria-hidden=\"true\">&times;</span>
      </button>
      $message
    </div>";
    }
    
    include("includes/scripts.php");
    ?>
  </body>
</html>