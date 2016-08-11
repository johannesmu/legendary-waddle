
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
      ?>
    </div>
    <div class="col-md-6 text-right">
      <span class="glyphicon glyphicon-shopping-cart">
      </span>
    </div>
  </div>
</div>

