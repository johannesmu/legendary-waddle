<?php
include("includes/session.php");
include("includes/dbconnection.php");
//if there is data being submitted via POST
if(count($_POST)>0){
  $email=$_POST["email"];
  $password=$_POST["password"];
  //sanitise variables, password should not be sanitised
  $email = filter_var($email,FILTER_SANITIZE_EMAIL);
  //check if email exists in database
  $query = "SELECT * FROM users WHERE email='$email'";
  $result = $dbconnection->query($query);
  if($result->num_rows!=0){
    echo "email already used";
  }
  else{
    //hash password
    $password = password_hash($password,PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email,password) VALUES ('$email','$password')";
    if($dbconnection->query($query)){
      //echo "success";
      $success=true;
    }
    else{
      echo "failure";
    }
  }
  
}
?>
<?doctype html>
<html>
<?php include("includes/head.php");?>
<body>
  <!--navigation section-->
    <?php include("includes/navigation.php");?>
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <h3>Register for an account</h3>
          <form id="register" method="POST" action="register.php">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email" id="email" placeholder="you@domain.com">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" id="password" placeholder="password">
            </div>
            <button type="submit" role="submit" class="btn btn-default">Register</button>
          </form>
          <?php
          if($success==true){
            echo "
            <div class=\"alert alert-success\">
              Your account has been created
            </div>";
          }
          ?>
        </div>
      </div>
    </div>
    <?php include("includes/scripts.php");?>
</body>
</html>