<?php
//include session
include("includes/session.php");
//include database connection
include ("includes/dbconnection.php");
//get user id from session
$userid = $_SESSION["id"];
//---------get shopping cart products from database
//this is to find out which items are already in the cart
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
$itemsperpage = 8;
//product query
$query = "SELECT * FROM products";
//if there is filter, append filter to query
if(count($_GET)>0){
  //get the number of page requested
  $page = $_GET["page"];
  //number of items to show per page
  
  //offset for database query
  $offset = $page*$itemsperpage;
  if($page){
    $query=$query." LIMIT $itemsperpage OFFSET $offset";
  }
  //the currently selected category
  $selectedcategory = $_GET["category"];
  
  if($selectedcategory){
      $query=$query." "."WHERE categoryid='$selectedcategory'";
  }
  $pricerange = $_GET["price"];
  if($pricerange && !$selectedcategory){
    $query=$query." "."WHERE price <= '$pricerange'";
  }
  if($pricerange && $selectedcategory){
    $query=$query." "."AND price <= '$pricerange'";
  }
}
//if no GET variables
else{
  $query = $query." LIMIT $itemsperpage";
  $page=0;
}
//echo $query;
//create an array to store products retrieved from database
$products = array();
//execute query and store in result variable
$result = $dbconnection->query($query);
if($result->num_rows>0){
  //total number of items
  $totalitems = $result->num_rows;
  //total number of pages of results
  $totalpages = ceil($totalitems/$itemsperpage);
  
  echo $totalitems."/".$totalpages;
  while($row = $result->fetch_assoc()){
    //check if item is in cart
    foreach($cartarray as $cartitem){
      if($row["id"]==$cartitem["productid"]){
        //if product exists in cart
        $row["cart"] = true;
      }
      else{
        $row["cart"] = false;
      }
    }
    //check if item is in wishlist
    foreach($wisharray as $wishitem){
      if($row["id"]==$wishitem["productid"]){
        $row["wish"] = true;
      }
      else{
        $row["wish"] = false;
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
                  if($current===$catid){
                    $class="active";
                  }
                  else{
                    $class="";
                  }
                  echo "<li class=\"$class\"><a href=\"index.php?category=$catid\">$catname</a></li>";
                }
                ?>
                </ul>
              <h5>Price Range</h5>
              <?php
              //set an array of maximum prices
              $prices = array(50,100,250,500);
              //get the current price range selected
              if($_GET["price"]){
                $selectedprice = $_GET["price"];
              }
              ?>
              <ul class="nav nav-stacked nav-pills">
                <?php
                if($_GET["price"]!=0){
                  $class="";
                }
                else{
                  $class="active";
                }
                echo "<li class=\"$class\"><a href=\"index.php?category=$selectedcategory&price=0\">
                  All Prices</span>
                  </a></li>";
                foreach($prices as $price){
                  //set class to active when the currently selected price matches one of the prices in the array
                  //this will "highlight" the currently selected price in the sidebar
                  if($selectedprice==$price){
                    $class="active";
                  }
                  else{
                    $class="";
                  }
                  echo "<li class=\"$class\"><a href=\"index.php?category=$selectedcategory&price=$price\">
                  under <span class=\"price price-filter\">$price</span>
                  </a></li>";
                }
                ?>
              </ul>
            </div>
            <div class="col-md-10">
              <?php
              //render products with row
              //count total number of products
              $totalproducts = count($products);
              //counter for product row
              $count = 0;
              foreach($products as $product){
                $name = $product["name"];
                $price = $product["price"];
                $id = $product["id"];
                $image = $product["image"];
                $count++;
                $productcount++;
                if($count==1){
                    echo "<div class=\"row product-row\">";
                }
                    echo "<div class=\"col-md-3 product\">
                    <h3>$name $count</h3>
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
                if($count>=4 || $productcount == $totalproducts){
                    echo "</div>";
                    $count = 0;
                }
              }
              ?>
              <!--bottom row for pagination-->
              <div class="row">
                <div class="col-md-12">
                  <nav aria-label="Page navigation" class="product-pagination">
                    <ul class="pagination">
                      <li>
                        <a href="#" aria-label="Previous">
                          <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>
                      <li><a href="#">1</a></li>
                      <li>
                        <a href="#" aria-label="Next">
                          <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
            </div>
        </div>
       <?php include("includes/scripts.php");?>
    </body>
</html>