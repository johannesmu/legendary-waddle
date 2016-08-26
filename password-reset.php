<?php
include("includes/session.php");
include("includes/dbconnection.php");
$userexists = false;
if($_POST["submit"]=="password-reset"){
  //check if user's email exists
  $email=filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
  $query = "SELECT email FROM users WHERE email='$email'";
  $result = $dbconnection->query($query);
  if($result->num_rows > 0){
    //let user reset their password
    $userexists = true;
  }
}

?>
<!doctype html>
<html>
  <?php
  include("includes/head.php");
  ?>
  <body>
    <?php include("includes/navigation.php");?>
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <h4>Reset your password</h4>
          <p>Please type your email address here and we will send an email to your registered email address.</p>
          <form id="password-reset" method="post" action="password-reset.php">
            <div class="form-group">
              <label for="email">Email address</label>
              <input class="form-control" name="email" id="email" type="email">
            </div>
            <button type="submit" class="btn btn-default" name="submit" value="password-reset">Reset your password</button>
          </form>
          <?php
          if($userexists && $_POST["submit"]){
            $class="alert-success";
            $message = "An email has been sent to your account";
          }
          elseif(!$userexists && $_POST["submit"]){
            $class="alert-warning";
            $message="Please retype your email address";
          }
          if($_POST["submit"]){
            echo "<div class=\"alert $class\">$message</div>";
          }
          
          ?>
        </div>
      </div>
    </div>
  </body>
</html>