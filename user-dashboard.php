<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if the user is not logged in, redirect to login page
if(!$_SESSION["email"]){
  header("location:login.php");
  //make sure we exit to stop the script from processing any further
  exit();
}
//receive update data
if(isset($_POST["submit"])){
  $submitted = true;
  $errors = array();
  $id = $_POST["id"];
  $newemail = filter_var($_POST["newemail"],FILTER_SANITIZE_EMAIL);
  $newpassword = $_POST["newpassword"];
  $repeatpassword = $_POST["newpassword1"];
  if($newpassword!=$repeatpassword && $newpassword!=""){
    $errors["password"] = "passwords not equal";
  }
  //check if new email is already used by another user with different id
  $query = "SELECT * FROM users WHERE email='$newemail' AND id<>'$id'";
  $checkresult = $dbconnection->query($query);
  if($checkresult->num_rows > 0){
    $errors["email"] = "the email address is unavailable";
  }
  //if there is no other user using the new email address
  //check if there are no errors
  if(count($errors)==0){
    //update user data
    //if both password and email have been filled in
    if($newpassword!="" && $newemail==""){
      $newpassword = password_hash($newpassword,PASSWORD_DEFAULT);
      $query = "UPDATE users SET password='$newpassword' WHERE id='$id'";
    }
    //if email is filled in but not the password
    elseif($newemail!="" && $newpassword==""){
      $query = "UPDATE users SET email='$newemail' WHERE id='$id'";
    }
    //if password is filled in but not email
    elseif($newemail!="" && $newpassword!=""){
      $newpassword = password_hash($newpassword,PASSWORD_DEFAULT);
      $query = "UPDATE users SET email='$newemail',password='$newpassword' WHERE id='$id'";
      $_SESSION["email"]=$newemail;
    }
    if($dbconnection->query($query)){
      $success = true;
      if($newemail!=""){
        $_SESSION["email"] = $newemail;
      }
    }
    else{
      $success = false;
    }
  }
}

//get user account data
$useremail = $_SESSION["email"];
$query = "SELECT * FROM users WHERE email='$useremail'";
$result = $dbconnection->query($query);

if($result->num_rows > 0){
  $userdata = $result->fetch_assoc();
}
$id = $userdata["id"];
$email = $userdata["email"];

//get users wishlist
$query = "SELECT 
wishlist.id as wishid,
products.id as productid,
products.name as name,
products.price as price,
products.image as image 
FROM wishlist 
INNER JOIN products 
ON wishlist.productid=products.id
WHERE wishlist.userid='$id'";

$wisharray = array();

$wishresult = $dbconnection->query($query);
if($wishresult->num_rows > 0){
  while($row = $wishresult->fetch_assoc()){
    array_push($wisharray,$row);
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
      <div class="col-md-4">
        <h4>Update Your Details</h4>
        <form id="user-details" method="post" action="user-dashboard.php">
          <div class="form-group">
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <input type="hidden" name="email" value="<?php echo $email;?>">
            
            <label for="email">Change Email Address</label>
            <input class="form-control" id="email" name="newemail" type="email" placeholder="<?php echo $email;?>">
          </div>
          <div class="form-group">
            <label for="password">Create A New Password</label>
            <input class="form-control" id="password" name="newpassword" type="password" placeholder="change password">
          </div>
          <div class="form-group">
            <label for="password1">Retype Password</label>
            <input class="form-control" id="password1" name="newpassword1" type="password" placeholder="retype new password">
          </div>
          <button class="btn btn-default" type="submit" name="submit">Change Your Details</button>
        </form>
        <?php
          if(count($errors)>0 && $submitted==true){
            echo "<div class=\"alert alert-warning\">";
            echo "update failed<br>";
              if($errors["email"]){
                echo $errors["email"]."<br>";
              }
              if($errors["password"]){
                echo $errors["password"];
              }
            echo "</div>";
          }
          elseif(count($errors)==0 && $submitted==true && $success == true){
            echo "<div class=\"alert alert-success\">Success</div>";
          }
          elseif(count($errors)==0 && $submitted==true && $success == false){
            echo "<div class=\"alert alert-danger\">Update Failed</div>";
          }
        ?>
      </div>
      <div class="col-md-8">
        <h4>Your Favourite Items</h4>
        <div class="btn-group">
          <a href="wishlist.php" class="btn btn-default">Manage your favourites</a>
          <a href="shopping-cart.php" class="btn btn-default">Manage your cart</a>
        </div>
        
        <!--list users wishlist here-->
        <?php
        $count=0;
        foreach($wisharray as $wish){
          $count++;
          $name = $wish["name"];
          $productid = $wish["productid"];
          $wishid = $wish["wishid"];
          $price = $wish["price"];
          $image = $wish["image"]; 
          if($count==1){
            echo "<div class=\"row\">";
          }
          echo "<div class=\"col-md-3\">
                  <h3>$name</h3>
                  <a href='detail.php?id=$productid'>
                    <img class='img-responsive product-image' src='images/$image'>
                  </a>
                  <p class='price product-price'>$price</p>
                  <a class='btn btn-default' href='detail.php?id=$productid'>detail</a>
                </div>";
          if($count>=4){
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