<?php
include("includes/session.php");
include("includes/dbconnection.php");
//check if user is admin
if(!$_SESSION["admin"]){
  echo "admin required";
  header("location: login.php");
  exit();
}
//image directory
$image_dir = "/images/";
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
  //remove illegal characters from title
  $title = filter_var($_POST["title"],FILTER_SANITIZE_STRING);
  //remove illegal characters from description
  $description = filter_var($_POST["description"],FILTER_SANITIZE_STRING);
  //remove illegal characters from quantity
  $quantity = filter_var($_POST["quantity"],FILTER_SANITIZE_NUMBER_INT);
  //remove illegal characters from category
  $productcategory = filter_var($_POST["category"],FILTER_SANITIZE_NUMBER_INT);
  //remove illegal characters from price
  $price = filter_var($_POST["price"],FILTER_SANITIZE_NUMBER_FLOAT);
  $imagefile = $_FILES["image"]["name"];
  //remove spaces from file name
  $imagefile = str_replace(' ','',$imagefile);
  //check the image upload
  
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
              </div>
            </div>
            <div class="row">
              <h4 class="col-md-12">Product Image</h4>
              <!--use a hidden input to send value to server whether image will be uploaded or selected from image already-->
              <!--on the server-- defaults to "1" which means image will be uploaded-->
              <input type="hidden" name="imageupload" value="1">
              <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active">
                    <a href="#upload" aria-controls="upload an image" role="tab" data-toggle="tab">Upload an image</a>
                  </li>
                  <li role="presentation">
                    <a href="#server" aria-controls="profile" role="tab" data-toggle="tab">Select an image from server</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="upload">
                    <div class="form-group">
                      <!--set maximum file size-->
                      <!--<input type="hidden" name="MAX_FILE_SIZE" value="5242880">-->
                      <label for="image">
                      Product Image
                      </label>
                      <input id="image" name="image" class="form-control" type="file">
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="server">
                    <div class="form-group">
                      <label for="selected-server-image">Selected Image</label>
                      <input class="form-control" name="selected-image" id="selected-server-image" readonly type="text">
                    </div>
                    <div class="col-md-12 server-gallery-container">
                      <div class="server-gallery">
                        <?php
                        $image_dir = "images";
                        $files = scandir("images");
                        foreach($files as $file){
                          if(is_dir($file)==false){
                            echo "<a href=\"#\" data-image=\"$file\">";
                            echo "<img class=\"server-image\" data-image=\"$file\" src=\"$image_dir"."/".$file."\">";
                            echo "</a>";
                          }
                        }
                        ?>
                      </div>
                    </div>
                  </div>
              </div>
              </div> 
            </div>
            <div class="row">
              <div class="col-md-12 text-right">
                <button class="btn btn-default" type="submit" name="submit" role="submit" value="submit">Create Item</button>
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
  <script>
    $(document).ready(function(){
      $(".server-gallery").click(function(e){
        //stop click from scrolling the page
        e.preventDefault();
        //get the image name from the data-image value on the images
        var image = $(e.target).data("image");
        //remove highlight from previously selected image
        $(e.target).parents(".server-gallery").find("img").removeClass("server-image-selected");
        //highlight the image that has been selected
        $(e.target).addClass("server-image-selected");
        //show the image name to the user
        $("#selected-server-image").val(image);
      });
    });
  </script>
</body>
</html>