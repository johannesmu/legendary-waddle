<?php
//uncomment for session debugging purposes
//print_r($_SESSION);
//navigation class
//the navigation class will control which navigation items are shown, depending on the status
//of the user, whether it is non authenticated, authenticated or admin
class Navigation{
  //get current file name
  private $_current;
  private $_emailsession;
  private $_adminsession;
  private $_connection;
  private $_navitems = array();
  private $_groups = array();
  private $_pagequery = "SELECT title,link,linktext,pagegroup FROM pages WHERE active=1";
  private $_groupsquery = "SELECT id,name FROM navgroup";
  public function __construct($connection,$emailsession,$adminsession){
    $this->_current = basename($_SERVER["REQUEST_URI"]);
    $this->_connection = $connection;
    $this->_emailsession = $emailsession;
    $this->_adminsession = $adminsession;
    if(!$emailsession){
      //
      $this->_pagequery = $this->_pagequery." AND needlogin=0";
    }
    if(!$adminsession){
      $this->_pagequery = $this->_pagequery." AND needadmin=0";
    }
    //order pages using column showorder
    $this->_pagequery = $this->_pagequery." ORDER BY showorder";
    $pageresult = $this->_connection->query($this->_pagequery);
    if($pageresult->num_rows > 0){
      while($navitem = $pageresult->fetch_assoc()){
        array_push($this->_navitems,$navitem);
      }
    }
    $groupsresult = $this->_connection->query($this->_groupsquery);
  }
  public function getItems(){
    return $this->_navitems;
  }
  public function __toString(){
    $items = array();
    $length = count($this->_navitems);
    for($i=0;$i<$length;$i++){
      $link = $this->_navitems[$i]["link"];
      $linktext = $this->_navitems[$i]["linktext"];
      //add active class to the current page
      if($link==$this->_current){
        $class = "class=\"active\"";
      }
      else{
        $class="";
      }
      //if user is logged in, remove the sign in page
      if($link=="login.php" && $this->_emailsession){
        $navitem = "";
      }
      else{
        $navitem = "<li $class><a href=\"$link\">$linktext</a></li>";
      }
      array_push($items,$navitem);
    }
    return implode($items);
  }
  public function getQuery(){
    //return $this->_pagequery;
  }
}

?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <?php
      //create navigation
      $navigation = new Navigation($dbconnection,$_SESSION["email"],$_SESSION["admin"]);
      //output navigation
      echo $navigation;
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
        //echo "<p>Hello Visitor, why not <a href=\"register.php\">join</a> our site?</p>";
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

