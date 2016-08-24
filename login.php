<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if there is data being submitted via POST
if(count($_POST)>0){
  $email=$_POST["email"];
  $password=$_POST["password"];
  //sanitise variables, password should not be sanitised
  $query = "SELECT * FROM users WHERE email='$email'";
  $result = $dbconnection->query($query);
  $userdata = $result->fetch_assoc();
  if(password_verify($password,$userdata["password"])){
    //login successful
    $stored_email = $userdata["email"];
    $loggedinuserid = $userdata["id"];
    //set success to true
    $success=true;
    //----------assign cart items to the logged in user
    //if there are items in the cart
    if(count($_SESSION["cart"])>0){
      //the id that the user has before logging in--from session id
      $currentuserid = $_SESSION["id"];
      
      //update all cart items that the user added before logging in to the current user
      $query="UPDATE cart SET userid='$loggedinuserid' WHERE userid='$currentuserid'";
      $dbconnection->query($query);
    }
    //if there is no item in the cart
    else{
      //check if there are any items in the user's cart in database
      $query = "SELECT * FROM cart WHERE userid='$loggedinuserid'";
      $result=$dbconnection->query($query);
      if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          array_push($_SESSION["cart"],$row);
        }
      }
    }
    //regenerate user id after logging in to prevent session fixation attack
    //see https://goo.gl/a6q56W
    session_regenerate_id();
    //create session variables using user data from database
    $_SESSION["email"]=$stored_email;
    $_SESSION["id"]=$userdata["id"];
    
    
    if($userdata["admin"]==='1'){
      $_SESSION["admin"]=1;
      header("location: dashboard.php");
    }
    else{
      header("location: user-dashboard.php");
    }
  }
  else{
    //echo "failure";
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
              echo "<div class=\"alert alert-danger\">Email or password does not match our records</div>";
            }
            ?>
          </form>
        </div>
      </div>
    </div>
    <?php include("includes/scripts.php");?>
</body>
</html>