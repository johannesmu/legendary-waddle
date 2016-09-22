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
//get cart items from SESSION
foreach($_SESSION["cart"] as $sessioncartitem){
  array_push($cartarray,$sessioncartitem);
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

//if there is price or category or page number, append to query
//check if there are any GET variables set

if(isset($_GET)){
  //-----the number of products to show on a page
  if(isset($_GET["itemsperpage"]) && $_GET["itemsperpage"]>0){
    $itemsperpage = $_GET["itemsperpage"];
  }
  else{
    //if variable is not set, set the default
    $itemsperpage = 8;
  }
  
  //-----which page of results to show
  if(isset($_GET["page"]) && $_GET["page"] > 0){
    $page = $_GET["page"];
  }
  //if not, set page to 1
  else{
    $page=1;
  }
  
  //-----which category to show
  if(isset($_GET["category"]) && $_GET["category"] > 0){
    $selectedcategory = $_GET["category"];
  }
  else{
    $selectedcategory = 0;
  }
  //-----price range
  if(isset($_GET["price"]) && $_GET["price"] > 0){
    $pricerange = $_GET["price"];
  }
  else{
    $pricerange = 0;
  }
  //----build query
  
  $query = "SELECT * FROM products";
  //if category number is larger than 0
  if($selectedcategory > 0){
      $query=$query." "."WHERE categoryid='$selectedcategory'";
  }
  //if price range is set and 'all categories' is selected
  if($pricerange > 0 && $selectedcategory==0){
    $query=$query." "."WHERE price <= '$pricerange'";
  }
  //if price range is set and a category is selected
  if($pricerange > 0 && $selectedcategory > 0){
    $query=$query." "."AND price <= '$pricerange'";
  }
  
  //before limit and offset is applied, get the total number of products
  //with category and price applied so we can get the total number of products
  //to display and total number of pages
  $totalresult = $dbconnection->query($query);
  if($totalresult->num_rows > 0){
    //total number of products
    $totalitems = $totalresult->num_rows;
    //total number of pages of results is total/item per page number
    $totalpages = ceil($totalitems/$itemsperpage);
  }
  //offset for database query to generate "pages" of data
  $offset =($page-1)*$itemsperpage;
  //append offset and items per page to query
  if($page){
    $query=$query." LIMIT $itemsperpage OFFSET $offset";
  }
}
//if no GET variables is set
else{
  $selectedcategory = 0;
  $pricerange = 0;
  $query = $query." LIMIT $itemsperpage";
  $page=1;
}



//create an array to store products retrieved from database
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
// print_r($products);

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
                  
                echo "<li class=\"$class\"><a href=\"index.php?category=0&page=$page&price=$pricerange\">all categories</a></li>";
                foreach($categories as $cat){
                  $catid = $cat["category_id"];
                  $catname = $cat["name"];
                  if($current===$catid){
                    $class="active";
                  }
                  else{
                    $class="";
                  }
                  echo "<li class=\"$class\"><a href=\"index.php?category=$catid&page=$page&price=$pricerange\">$catname</a></li>";
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
                echo "<li class=\"$class\"><a href=\"index.php?category=$selectedcategory&price=0&page=1\">
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
                  echo "<li class=\"$class\"><a href=\"index.php?category=$selectedcategory&price=$price&page=1\">
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
                    <h4>$name $count</h4>
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
              
              
            </div>
            </div>
            <!--bottom row for pagination-->
            <div class="row">
                <div class="col-md-12">
                  <nav aria-label="Page navigation" class="product-pagination">
                    <ul class="pagination">
                      <li>
                        <?php
                        if($totalpages > 1 && $page > 1){
                          $prevpage = $page-1;
                          echo 
                          "<a href=\"index.php?category=$selectedcategory&price=$pricerange&page=$prevpage\" aria-label=\"Previous\">
                            <span aria-hidden=\"true\">&laquo;</span>
                          </a>";
                        }
                        
                        ?>
                      </li>
                      <?php
                      if($totalpages>1){
                        for($i=0;$i<$totalpages;$i++){
                          $pagenumber = $i+1;
                          if($pagenumber==$page){
                            $class="active";
                          }
                          else{
                            $class="";
                          }
                          echo "<li class=\"$class\"><a href=\"index.php?category=$selectedcategory&price=$pricerange&page=$pagenumber\">$pagenumber</a></li>";
                        }
                      }
                      
                      if($totalpages>1 && $page<$totalpages){
                        $nextpage = $page+1;
                      echo "<li>
                        <a href=\"index.php?category=$selectedcategory&price=$pricerange&page=$nextpage\" aria-label=\"Next\">
                          <span aria-hidden=\"true\">&raquo;</span>
                        </a>
                      </li>
                      ";
                      }
                      ?>
                    </ul>
                  </nav>
                </div>
              </div>
        </div>
       <?php include("includes/scripts.php");?>
    </body>
</html>