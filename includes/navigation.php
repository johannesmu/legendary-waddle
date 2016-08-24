<?php
//uncomment for session debugging purposes
//print_r($_SESSION);
?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <li><a href="index.php">Home</a></li>
      <?php
      //if user is not logged in
      if(!$_SESSION["email"]){
        echo "<li><a href=\"register.php\">Register</a></li>";
        echo "<li><a href=\"login.php\">Sign In</a></li>";
      }
      ?>
      <?php
      //if the user is logged in
      if($_SESSION["email"]){
        //if the user is an admin, show the dashboard
        if($_SESSION["admin"]){
          echo "<li><a href=\"dashboard.php\">Dashboard</a></li>";
        }
        echo "<li><a href=\"user-dashboard.php\">My Account</a></li>";
        echo "<li><a href=\"logout.php\">Logout</a></li>";
      }
      ?>
    </ul>
    <form class="navbar-form navbar-right" action="search.php" method="get">
      <div class="form-group">
        <input type="text" name="search" class="form-control" placeholder="Search">
        <button type="submit" role="search" class="btn btn-default">
          Search
        </button>
      </div>
    </form>
  </div>
</nav>
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <?php
      if($_SESSION["email"]){
        echo "<p>Hello ".$_SESSION["email"]."</p>";
      }
      else{
        echo "<p>Hello Visitor, why not <a href=\"register.php\">join</a> our site?</p>";
      }
      ?>
    </div>
      <?php
      //get user id
      $userid = $_SESSION["id"];
      //count cart items in database
      $cartquery = "SELECT productid,quantity FROM cart WHERE userid='$userid'";
      $result = $dbconnection->query($cartquery);
      //count items in shopping cart
      $carttotal = 0;
      //if there are items in the cart
      if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          $carttotal+=$row["quantity"];
        }
      }
      //count items in the wishlist
      $wishtotal = 0;
      $wishquery = "SELECT productid FROM wishlist WHERE userid='$userid'";
      $result = $dbconnection->query($wishquery);
      if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          $wishtotal++;
        }
      }
      ?>
    <div class="col-md-6 text-right">
      <a href="shopping-cart.php" class="btn btn-default shopping-cart shop-buttons">
        <span class="glyphicon glyphicon-shopping-cart"></span>
        <!--keep the line below as a single line to allow empty badge when there is no-->
        <!--item in the cart-->
        <span class="badge cart-total"><?php if($carttotal>0){echo $carttotal;}?></span>
      </a>
      <a href="wishlist.php" class="btn btn-default wishlist shop-buttons">
        <span class="glyphicon glyphicon-star"></span>
        <!--keep the line below as a single line to allow empty badge when there is no-->
        <!--item in the cart-->
        <span class="badge wish-total"><?php if($wishtotal>0){echo $wishtotal;}?></span>
      </a>
      
    </div>
  </div>
</div>

