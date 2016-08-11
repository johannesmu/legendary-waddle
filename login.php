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
    //echo "success"." hello $stored_email";
    $success=true;
    $_SESSION["email"]=$stored_email;
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