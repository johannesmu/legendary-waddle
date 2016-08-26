<?php
include("includes/session.php");
include("includes/dbconnection.php");

//----------display cart items
//create array to store cart contents
$wisharray = array();
//get cart contents from the database by joining products and cart tables
//so we can get images and description
$query = "SELECT 
          wishlist.id AS id,
          wishlist.productid AS productid,
          products.image AS image,
          products.name AS name,
          products.description AS description,
          products.price AS price
          FROM wishlist 
          INNER JOIN products 
          ON products.id = wishlist.productid
          WHERE wishlist.userid = '$userid'";
$result = $dbconnection->query($query);
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    array_push($wisharray,$row);
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
        <div class="col-md-12">
          
        </div>
      </div>
    </div>
  </body>
</html>