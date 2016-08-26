<?php
//include session
include("includes/session.php");
//include database connection
include ("includes/dbconnection.php");
//get user id from session
$userid = $_SESSION["id"];
//---------get shopping cart products
$cartarray = array();
$cartquery = "SELECT productid FROM cart WHERE userid='$userid'";
$cartresult = $dbconnection->query($cartquery);
if($cartresult->num_rows>0){
  while($row=$cartresult->fetch_assoc()){
    array_push($cartarray,$row);
  }
}
$cartlength = count($cartarray);
//---------get wishlist products
$wisharray = array();
$wishquery = "SELECT productid FROM cart WHERE userid='$userid'";
$wishresult = $dbconnection->query($wishquery);
if($wishresult->num_rows>0){
  while($row=$wishresult->fetch_assoc()){
    array_push($wisharray,$row);
  }
}
$wishlength = count($wisharray);
//---------get products
//product query
$query = "SELECT * FROM products";
//if there is filter, append filter to query
if(count($_GET)>0){
    $selectedcategory = $_GET["category"];
    if($selectedcategory){
        $query=$query." "."WHERE categoryid='$selectedcategory'";
    }
}
//create an array to store products
$products = array();
//execute query and store in result variable
$result = $dbconnection->query($query);
if($result->num_rows>0){
    while($row = $result->fetch_assoc()){
        //check if item is in cart
        foreach($cartarray as $cartitem){
          if($row["id"]==$cartitem["productid"]){
            //if product exists in cart
            $row["cart"] = true;
          }
        }
        //check if item is in wishlist
        foreach($wisharray as $wishitem){
          if($row["id"]==$wishitem["productid"]){
            $row["wish"] = true;
          }
        }
        array_push($products,$row);
    }
}

//get categories
$catquery = "SELECT * FROM categories";
$catresult = $dbconnection->query($catquery);
$categories = array();
if($catresult->num_rows>0){
    while($row = $catresult->fetch_assoc()){
        array_push($categories,$row);
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
            <div class="col-md-2">
              <h5>Filter by Categories</h5>
                <ul class="nav nav-stacked nav-pills">
                <?php
                //get the current category being shown
                if($_GET["category"]){
                  $current = $_GET["category"];
                  $class="";
                }
                else{
                  $class="active";
                }
                  
                  echo "<li class=\"$class\"><a href=\"index.php\">all categories</a></li>";
                  foreach($categories as $cat){
                    $catid = $cat["category_id"];
                    $catname = $cat["name"];
                    if($current===$catid){$class="active";}else{$class="";}
                    echo "<li class=\"$class\"><a href=\"index.php?category=$catid\">$catname</a></li>";
                  }
                ?>
                </ul>
            </div>
            <div class="col-md-10">
              <?php
              //render products with row
              $count = 0;
              foreach($products as $product){
                $name = $product["name"];
                $price = $product["price"];
                $id = $product["id"];
                $image = $product["image"];
                $count++;
                if($count==1){
                    echo "<div class=\"row product-row\">";
                }
                    echo "<div class=\"col-md-4 product\">
                    <h3>$name</h3>
                    <a href='detail.php?id=$id'>
                    <img class='product-image' src='images/$image'>
                    </a>
                    <p class='price product-price'>$price</p>
                    <a class='btn btn-default' href='detail.php?id=$id'>
                    detail
                    </a>";
                    if($product["cart"]==true){
                      echo "<span class=\"badge\"><i class=\"glyphicon glyphicon-shopping-cart\"></i>
                      in cart
                      </span>";
                    }
                    if($product["wish"]==true){
                      echo "<span class=\"badge\"><i class=\"glyphicon glyphicon-star\"></i>
                      in wishlist
                      </span>";
                    }
                    echo "</div>";
                if($count>=3){
                    echo "</div>";
                    $count = 0;
                }
              }
              ?>
            </div>
            </div>
        </div>
       <?php include("includes/scripts.php");?>
    </body>
</html>