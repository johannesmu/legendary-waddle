<?php
include("includes/session.php");
include("includes/dbconnection.php");

$userid = $_SESSION["id"];
//----------display cart items
//create array to store cart contents
$wisharray = array();
//get cart contents from the database by joining products and cart tables
//so we can get images and description
$query = "SELECT 
          wishlist.id AS id,
          wishlist.productid AS productid,
          wishlist.time AS time,
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
          <h3>Your Favourite Items</h3>
        </div>
      </div>
      <div class="row">
        <?php
        foreach($wisharray as $wish){
        $id = $wish["id"];
        $name = $wish["name"];
        $price = $wish["price"];
        $time = $wish["time"];
        $productid = $wish["productid"];
        $image = $wish["image"];
        $description = $wish["description"];
        //work out how long ago the product was added
        $now = new DateTime(generateDateTime());
        $storedtime = new DateTime($time);
        $ago = $now->diff($storedtime);
        echo "<div class=\"col-md-2\">
        <h4>$name</h4>
          <img class=\"img-responsive\" src=\"images/$image\">
          <p class=\"price\">$price</p>
          <p>This product was added $ago->format('%y years %m months %a days %h hours %i minutes %S seconds'') ago</p>
        </div>";
        }
        ?>
      </div>
    </div>
  </body>
</html>