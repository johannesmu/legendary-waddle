<?php
if(count($_GET)>0){
    $id=$_GET["id"];
    if(is_numeric($id)){
      //sanitise id to prevent XSS
      $id = filter_var($id,FILTER_SANITIZE_NUMBER_INT);
      include ("includes/dbconnection.php");
      $query = "SELECT * FROM products WHERE id='$id'";
      $product = array();
      $result = $dbconnection->query($query);
      if($result->num_rows>0){
        $product = $result->fetch_assoc();
      }
    }
    else{
      echo "failed";
      exit();
    }
}
else{
    echo "no product specified";
}

?>
<!doctype html>
<html>
    <?php include("includes/head.php");?>
    <body>
        <?php include("includes/navigation.php");?>
        <div class="container">
            <?php
            $name = $product["name"];
            $price = $product["price"];
            $id = $product["id"];
            $description = $product["description"];
            $image = $product["image"];
            $stockqty = $product["stockqty"];
            echo "<div class='row'>
                    <h3 class='col-md-12'>$name</h3>
                </div>";
                
            echo 
                "<div class='row'>
                <div class='col-md-4'>
                    <img class='product-image' src='images/$image'>
                </div>
                <div class='col-md-8'>
                    <p>$description</p>
                    <p class='price'>$price</p>
                    <button data-id='$id' class='btn btn-default'>Add to cart</button>
                </div>
                </div>";
            ?>
           
        </div>
        <?php include("includes/scripts.php"); ?>
    </body>
</html>