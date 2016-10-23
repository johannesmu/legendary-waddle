<?php
//database class. initialize with $host and $dbname
class Database{
  private $_connection;
  private $_server;
  private $_user;
  private $_password;
  private $_db;
  private $status;
  public function __construct($host,$dbname){
    //get username and password from environmental variables
    //set in apache
    $this->_user = getenv('dbuser');
    $this->_password = getenv('dbpassword');
    $this->_server = $host;
    $this->_db = $dbname;
    $this->_connection = new mysqli($this->_server, $this->_user, $this->_password, $this->_db);
    if(mysqli_connect_error()) {
			trigger_error("Failed to conenct to to MySQL: " . mysqli_connect_error(),
				 E_USER_ERROR);
			$this->status = false;
		}
		else{
		  $this->status = true;
		}
  }
  
  private function __clone(){}
  public function __toString(){
    return "Database class";
  }
  public function getStatus(){
    return (string)$this->status;
  }
  public function getConnection(){
    return $this->_connection;
  }
}

class User{
  private $_connection;
  private $_isauth;
  private $_level;
  private $_errors = array();
  public $status;
  public function __construct($conn){
    $this->_connection = $conn;
  }
  public function register($email,$password){
    $email = filter_var($email,FILTER_SANITIZE_EMAIL);
    //check if email is already used
    $query = "SELECT COUNT(email) FROM users WHERE email='$email'";
    $result = $this->_connection->query($query);
    $total = $result->fetch_assoc();
    if($total["total"]==0){
      //the email does not exist in the database so
      //we hash the password
      $password = password_hash($password,PASSWORD_DEFAULT);
      //we insert the new user to database
      //generate User ID
      //generate password_reset string
      $query = "INSERT INTO users (email,password,created,lastlogin) VALUES ('$email','$password',NOW(),NOW())";
      $result = $this->_connection->query($query);
      return true;
    }
    else{
      //if the email exists
      $this->_errors["message"] = "email address is already used";
      $status = false;
      return false;
    }

  }
}

//navigation  class uses the database connection
class Navigation{
  //get current file name
  private $_current;
  private $_usersession;
  private $_adminsession;
  private $_connection;
  private $_navitems = array();
  private $_groups = array();
  private $_pagequery = "SELECT title,link,linktext,pagegroup FROM pages WHERE active=1";
  private $_groupsquery = "SELECT id,name FROM navgroup";
  public function __construct(){
    $this->_current = basename($_SERVER["REQUEST_URI"]);
    $this->_connection = $connection;
    $this->_usersession = $usersession;
    $this->_adminsession = $adminsession;
    if(!$usersession){
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
      if($link=="login.php" && $this->_usersession){
        $navitem = "";
      }
      else{
        $navitem = "<li $class><a href=\"$link\">$linktext</a></li>";
      }
      array_push($items,$navitem);
    }
    return implode($items);
  }
  
}

class Generator{
  public $result;
  public function __construct($length){
    $this->result = bin2hex(openssl_random_pseudo_bytes($length));
    return $this->result;
  }
}
?>