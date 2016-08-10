<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if user is not admin
if(!$_SESSION["admin"]){
  //if user is logged in
  if($_SESSION["email"]){
    header("location: user-dashboard.php");
  }
  //if user is not logged in redirect to login page
  else{
    header("location: login.php");
  }
  exit();
}
//get products to show
$query = "SELECT * FROM products";
$products = array();
$result = $dbconnection->query($query);
if($result->num_rows>0){
  while($row=$result->fetch_assoc()){
    array_push($products,$row);
  }
}
//get categories for the new image form
$catquery = "SELECT * FROM categories";
$categories = array();
$catresult = $dbconnection->query($catquery);
if($catresult->num_rows > 0){
  while($catrow=$catresult->fetch_assoc()){
    array_push($categories,$catrow);
  }
}
//get users from database
$userquery = "SELECT * FROM users";
$users = array();
$userresult = $dbconnection->query($userquery);
if($userresult->num_rows > 0){
  while($userrow=$userresult->fetch_assoc()){
    array_push($users,$userrow);
  }
}
//handle the file upload
//if there is post data sent to this page
if(count($_POST)>0){
  //get all data posted
  $name = $_POST["name"];
  $description = $_POST["description"];
  $quantity = $_POST["quantity"];
  $category = $_POST["category"];
  $imagefile = $_FILES["image"]["name"];
  var_dump($_FILES["image"]);
  if ($_FILES['image']['size'] > 5242880) {
    echo "file too large";
  }
  else{
    //check if it is an image
    //rename the image
    //move image
    //store reference in database
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
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#manageproducts" aria-controls="manage products" role="tab" data-toggle="tab">Manage Products</a>
            </li>
            <li role="presentation">
              <a href="#manageusers" aria-controls="manage users" role="tab" data-toggle="tab">Manage Users</a>
            </li>
            <li role="presentation">
              <a href="#statistics" aria-controls="statistics" role="tab" data-toggle="tab">Statistics</a>
            </li>
            <li role="presentation">
              <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a>
            </li>
          </ul>
        </div>
        <div class="col-md-12">
          <div class="tab-content">
            <!--Manage products-->
            <div role="tabpanel" class="tab-pane active" id="manageproducts">
              <div class="col-md-3">
                <h3>Add a new Product</h3>
                <a class="btn btn-default" href="add_product.php">Add</a>
              </div>
              <div class="col-md-8">
              <h3>Existing Products</h3>
                <?php
                foreach($products as $product){
                  $id=$product["id"];
                  $name=$product["name"];
                  $description=$product["description"];
                  $price=$product["price"];
                  $image=$product["image"];
                  $stock=$product["stockqty"];
                  echo 
                  "<div class=\"row product-row\">
                    <div class=\"col-md-2\">
                      <img class=\"img-responsive\" src=\"images/$image\">
                    </div>
                    <div class=\"col-md-7\">
                      <h4>$name</h4>
                      <p class=\"price\">$price</p>
                      <p>quantity available:$stock</p>
                    </div>
                    <div class=\"col-md-3 text-right\">
                    <form id='product-edit'>
                      <div class=\"btn-group-vertical\">
                        <button class=\"btn btn-success\">Edit</button>
                        <button class=\"btn btn-warning\">Delete</button>
                      </div>
                    </form>
                    </div>
                  </div>";
                }
                ?>
                
              </div>
            </div>
            <!--Add Product-->
            <!--<div role="tabpanel" class="tab-pane" id="addproduct">-->
            <!--  <h3>Add a new item</h3>-->
            <!--    <form id="new-item" action="dashboard.php" method="POST" enctype="multipart/form-data">-->
            <!--      <div class="row">-->
            <!--        <div class="col-md-6">-->
            <!--          <div class="form-group">-->
            <!--            <label for="name">Item Name</label>-->
            <!--            <input id="name" name="name" class="form-control" type="text" placeholder="Item Name">-->
            <!--          </div>-->
            <!--          <div class="form-group">-->
            <!--            <label for="description">Item Description</label>-->
            <!--            <textarea id="description" name="description" class="form-control" placeholder="Item Description"></textarea>-->
            <!--          </div>-->
            <!--          <div class="form-group">-->
            <!--            <label for="quantity">Quantity in stock</label>-->
            <!--            <input id="quantity" name="quantity" class="form-control number" type="number" value="10">-->
            <!--          </div>-->
            <!--          <div class="form-group">-->
            <!--            <label for="category">Item Category</label>-->
            <!--            <select id="category" class="form-control" name="category">-->
            <!--              <?php-->
            <!--              foreach($categories as $cat){-->
            <!--                $id = $cat["category_id"];-->
            <!--                $name = $cat["name"];-->
            <!--                echo "<option value=\"$id\">$name</option>";-->
            <!--              }-->
            <!--              ?>-->
            <!--            </select>-->
            <!--          </div>-->
            <!--        </div>-->
            <!--        <div class="col-md-6">-->
            <!--          <div class="form-group">-->
            <!--            <label for="image">-->
            <!--            Product Image-->
            <!--            </label>-->
            <!--            <input id="image" name="image" class="form-control" type="file">-->
            <!--          </div>-->
            <!--        </div>-->
                    
            <!--      </div>-->
            <!--      <button class="btn btn-default" type="submit" role="submit">Create Item</button>-->
            <!--    </form>-->
              
            <!--</div>-->
            <!--Manage Users-->
            <div role="tabpanel" class="tab-pane" id="manageusers">
              <h4>Site Users</h4>
              <?php
              foreach($users as $user){
                $id = $user["id"];
                $email = $user["email"];
                $password = $user["password"];
                
                echo 
                "<div class='row'>
                  <form id='user-$id' action='edit_user.php' method='post'>
                  <div class='col-md-6'>
                    <div class='form-group'>
                      <label for='email-$id'>User Email</label>
                      <p>$email</p>
                    </div>
                  </div>
                  <div class='col-md-2'>
                    <div class='form-group'>
                      <label>Edit User</label>
                      <button name='submit' data-id='$id' type='submit' role='submit' class='form-control btn btn-default'>
                        Edit User
                      </button>
                    </div>
                  </div>
                  </form>
                </div>";
              }
              ?>
            </div>
            <!--Statistics-->
            <div role="tabpanel" class="tab-pane" id="statistics">...</div>
          </div>
        </div>
        
      </div>
    </div>
    <?php include("includes/scripts.php");?>
</body>
</html>