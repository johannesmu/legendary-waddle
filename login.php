<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if there is data being submitted via POST
if(count($_POST)>0){
  $email=$_POST["email"];
  $password=$_POST["password"];
  //sanitise variables, password should not be sanitised
  $query = "SELECT * FROM users WHERE email='$email' AND active='1'";
  $result = $dbconnection->query($query);
  $userdata = $result->fetch_assoc();
  if(password_verify($password,$userdata["password"])){
    //login successful
    $stored_email = $userdata["email"];
    $loggedinuserid = $userdata["id"];
    //set success to true
    $success=true;
    //set last login time for user
    //generateDateTime function is in session.php file
    $lastlogin = generateDateTime();
    $logintimequery = "UPDATE users SET lastlogin='$lastlogin' WHERE id='$loggedinuserid'";
    $dbconnection->query($logintimequery);
    
    //----------assign cart items and wishlist to the logged in user
    //the id that the user has before logging in--from session id
    $currentuserid = $_SESSION["id"];
    //find cart items in the database
    $cartquery = "SELECT productid FROM cart WHERE userid='$currentuserid'";
    $cartresult = $dbconnection->query($cartquery);
    //if there are items in the cart
    if($cartresult->num_rows > 0){
      //update all cart items that the user added before logging in to the current user
      $query="UPDATE cart SET userid='$loggedinuserid' WHERE userid='$currentuserid'";
      $dbconnection->query($query);
    }
    $wishquery = "SELECT productid FROM wishlist WHERE userid='$currentuserid'";
    $wishresult = $dbconnection->query($wishquery);
    if($wishresult->num_rows > 0){
      //update all wish list items that the user has added and assign to current user id
      $query="UPDATE wishlist SET userid='$loggedinuserid' WHERE userid='$currentuserid'";
      $dbconnection->query($query);
      //merge duplicate products, if found
      $wisharray = array();
      $query = "SELECT * FROM wishlist WHERE userid='$currentuserid'";
      $records = $dbconnection->query($query);
      if($records->num_rows > 0){
        //add the wishlist items to array
        while($row = $records->fetch_assoc()){
          array_push($wisharray,$row);
        }
        
      }
    }
    
    //regenerate user id after logging in to prevent session fixation attack
    //see https://goo.gl/a6q56W
    session_regenerate_id();
    //create session variables using user data from database
    $_SESSION["email"]=$stored_email;
    $_SESSION["id"]=$userdata["id"];
    
    //if user is an admin
    if($userdata["admin"]==='1'){
      $_SESSION["admin"]=1;
      //redirect to admin dashboard
      header("location: dashboard.php");
    }
    //if user is not an admin
    else{
      //redirect to user dashboard
      header("location: user-dashboard.php");
    }
  }
  else{
    $success=false;
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
        <div class="col-md-6 col-md-offset-3">
          <h3>Log in to your account</h3>
          <form id="login" method="POST" action="login.php">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email" id="email" placeholder="you@domain.com">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" id="password" placeholder="password">
            </div>
            <button type="submit" role="submit" class="btn btn-default">Log in</button>
            <?php
            if($success===true){
              echo "<div class=\"alert alert-success\">Welcome</div>";
            }
            elseif($success===false){
              echo "<div class=\"alert alert-danger\">Authentication Error</div>";
            }
            ?>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <p>
            Don't have an account? <a href="register.php">Sign up</a> here
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <p>
            Forgot your password? <a href="password-reset.php">Reset your password</a> here
          </p>
        </div>
      </div>
    </div>
    <?php include("includes/scripts.php");?>
</body>
</html>