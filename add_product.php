<?php
include("includes/session.php");
include("includes/dbconnection.php");
//check if user is admin
if(!$_SESSION["admin"]){
  echo "admin required";
  exit();
}
//image directory
$image_dir = "images/";
//get categories
$catquery = "SELECT category_id,name FROM categories";
$result = $dbconnection->query($catquery);
$categories = array();
if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
    array_push($categories,$row);
  }
}

if(count($_POST)>0){
  $title = filter_var($_POST["title"],FILTER_SANITIZE_STRING);
  $description = filter_var($_POST["description"],FILTER_SANITIZE_STRING);
  $quantity = filter_var($_POST["quantity"],FILTER_SANITIZE_STRING);
  $productcategory = filter_var($_POST["category"],FILTER_SANITIZE_NUMBER_INT);
  $price = filter_var($_POST["price"],FILTER_SANITIZE_NUMBER_INT);
  $imagefile = filter_var($_FILES["image"]["name"],FILTER_SANITIZE_STRING);
  $imagefile = str_replace(' ','',$imagefile);
  //check the image upload
  if (isset($_FILES["image"])) {
    $tempFile = $_FILES["image"]["tmp_name"];
    echo "temp=$tempFile<br>$imagefile<br>";
    print_r($_FILES["image"]["error"]);
    //$fileName = // determine secure name for uploaded file
    $fileName = uniqid().$imagefile;
    list($width, $height) = getimagesize($tempFile);
    // check if the file is really an image
    if ($width == null && $height == null) {
        // header("Location: index.php");
        // return;
        echo "not an image";
        exit();
    }
    // resize if necessary
    if ($width >= 400 && $height >= 400) {
        $image = new Imagick($tempFile);
        $image->thumbnailImage(400, 400);
        $image->writeImage($fileName);
    }
    else {
        $fileName = $image_dir.$fileName;
        move_uploaded_file($tempFile, $fileName);
        //add product to database
        $query = "INSERT INTO products (name,description,price,stockqty,categoryid,image) 
        VALUES ('$name','$description','$stockqty','$price','$productcategory','$imagefile')";
        if($dbconnection->query($query)){
          $success = true;
        }
        else{
          echo "product creation failed";
        }
    }
  }
  else{
    echo "please add a product image";
    exit();
  }
}
?>
<?doctype html>
<html>
<?php include("includes/head.php");?>
<body>
  <?php include("includes/navigation.php");?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div role="tabpanel" class="tab-pane" id="addproduct">
        <h3>Add a new item</h3>
          <form id="new-item" action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Item Name</label>
                  <input id="name" name="name" class="form-control" type="text" placeholder="Item Name">
                </div>
                <div class="form-group">
                  <label for="description">Item Description</label>
                  <textarea id="description" name="description" class="form-control" placeholder="Item Description"></textarea>
                </div>
                <div class="form-group">
                  <label for="quantity">Quantity in stock</label>
                  <input id="quantity" name="quantity" class="form-control number" type="number" value="10">
                </div>
                
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price">Price</label>
                  <input id="price" name="price" class="form-control number" type="number" step="0.01" min="0.01" placeholder="99.99">
                </div>
                <div class="form-group">
                  <label for="category">Item Category</label>
                  <select id="category" class="form-control" name="category">
                    <?php
                    foreach($categories as $cat){
                      $id = $cat["category_id"];
                      $name = $cat["name"];
                      echo "<option value=\"$id\">$name</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <!--set maximum file size-->
                  <!--<input type="hidden" name="MAX_FILE_SIZE" value="5242880">-->
                  <label for="image">
                  Product Image
                  </label>
                  <input id="image" name="image" class="form-control" type="file">
                </div>
                <div class="text-right">
                  <button class="btn btn-default" type="submit" name="submit" role="submit">Create Item</button>
                </div>
              </div>
            </div>
          </form>
          <?php
          if($success){
            echo "<div class=\"alert alert-success\">Product Created</div>";
          }
          ?>
      </div>
      </div>
    </div>
  </div>
  <?php include("includes/scripts.php");?>
</body>
</html>