<?php
include("includes/session.php");
include ("includes/dbconnection.php");
//get products
//product query
$query = "SELECT * FROM products";
//if there is filter, append filter to query
if(count($_GET)>0){
    $selectedcategory = $_GET["category"];
    if($selectedcategory){
        $query=$query." WHERE categoryid='$selectedcategory'";
    }
}
$products = array();
$result = $dbconnection->query($query);
if($result->num_rows>0){
    while($row = $result->fetch_assoc()){
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
                    <h4>Filter by Categories</h4>
                    <form id="categories" method="GET" action="index.php">
                        <select class="form-control" name='category'>
                        <?php
                            foreach($categories as $cat){
                                $catid = $cat["category_id"];
                                $catname = $cat["name"];
                                echo "<option value='$catid'>$catname</option>";
                            }
                            echo "<option value='0'>all</option>";
                        ?>
                        </select>
                        <button type="submit" role="submit" class="btn btn-default">Filter</button>
                    </form>
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <?php
                        foreach($products as $product){
                            $name = $product["name"];
                            $price = $product["price"];
                            $id = $product["id"];
                            $image = $product["image"];
                            echo "<div class=\"col-md-4\">
                            <h3>$name</h3>
                            <img class='product-image' src='images/$image'>
                            <p class='price'>$price</p>
                            <a class='btn btn-default' href='detail.php?id=$id'>detail</a>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
       <?php include("includes/scripts.php");?>
    </body>
</html>