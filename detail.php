<?php
include("includes/session.php");
include("includes/dbconnection.php");
if(count($_GET)>0){
    $id=$_GET["id"];
    if(is_numeric($id)){
      //sanitise id
      $id = filter_var($id,FILTER_SANITIZE_NUMBER_INT);
      //find the product with the id requested
      $query = "SELECT * FROM products WHERE id='$id'";
      $product = array();
      $result = $dbconnection->query($query);
      //if product exists
      if($result->num_rows>0){
        $product = $result->fetch_assoc();
      }
    }
}
else{
    exit();
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
            ?>
            <div class="row">
              <h3 class="col-md-12">
                <?php echo $name;?>
              </h3>
            </div>
            <div class="row">
              <div class="col-md-5">
                  <img class="product-image" 
                  src="images/<?php echo $image;?>">
              </div>
              <div class="col-md-7 product-description">
                  <p><?php echo $description;?></p>
                  <p class="price"><?php echo $price;?></p>
                <div class="row">
                  <div class="col-md-12">
                    <form method="post" class="form-inline detail-form" action="additem.php">
                      <input type="number" class="form-control quantity" name="quantity" value="1">
                      <input type="hidden" name="productid" value="<?php echo $id;?>">
                      <button type="submit" name="submit" value="cart" data-id="<?php echo $id;?>" class="btn btn-default">
                      <span class="glyphicon glyphicon-shopping-cart"></span>
                      Add to cart
                      </button>
                      <button type="submit" name="submit" value="wish" data-id="<?php echo $id;?>" class="btn btn-default">
                      <span class="glyphicon glyphicon-star"></span>
                      Add to wishlist
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <?php
            $showalert = false;
            if($_GET["success"]==true){
              $class="alert-success";
              $message="Item added";
              $showalert="true";
            }
            elseif($_GET["success"]===false){
              $class="alert-warning";
              $message="Item cannot be added, due to an error";
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
            ?>
            
           
        </div>
        <?php include("includes/scripts.php"); ?>
    </body>
</html>