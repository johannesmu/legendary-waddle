<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if user is not admin
if(!$_SESSION["admin"]){
  //if user is logged in redirect to user-dashboard
  if($_SESSION["email"]){
    header("location: user-dashboard.php");
  }
  //if user is not logged in redirect to login page
  else{
    header("location: login.php");
  }
  //stop if the above conditions are not met
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
            <!--manage products-->
            <li role="presentation" class="active">
              <a href="#manageproducts" aria-controls="manage products" role="tab" data-toggle="tab">Manage Products</a>
            </li>
            <!--manage users-->
            <li role="presentation">
              <a href="#manageusers" aria-controls="manage users" role="tab" data-toggle="tab">Manage Users</a>
            </li>
            <!--manage categories-->
            <li role="presentation">
              <a href="#categories" aria-controls="manage categories" role="tab" data-toggle="tab">Manage Categories</a>
            </li>
            <!--settings-->
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
            <!--Manage Categories-->
            <div role="tabpanel" class="tab-pane" id="categories">
              <div class="row">
                <?php
                foreach($categories as $category){
                  $catid = $category["id"];
                  $catname = $category["name"];
                  echo "<div class=\"col-md-12 panel\" data-id=\"$id\">
                  <p>$name</p>
                  <div>";
                }
                ?>
              </div>
            </div>
            <!--Settings-->
            <div role="tabpanel" class="tab-pane" id="settings">...</div>
          </div>
        </div>
        
      </div>
    </div>
    <?php include("includes/scripts.php");?>
</body>
</html>