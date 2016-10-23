<?php
//this class needs a database connection upon construction
class User{
  public $isauth;
  private $connection;
  private $response = array();
  public function __construct($db){
    $this->connection = $db->getConnection();
  }
  public function register($username,$email,$password,$resetstring){
    //sanitize email address
    $email=filter_var($email,FILTER_SANITIZE_EMAIL);
    //check if email is valid after sanitization
    if(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
      $this->response["status"] = false;
      $this->response["message"] = "invalid email address";
      return $this->response;
    }
    //check if email is already used
    $query = "SELECT email FROM users WHERE email='$email'";
    $result = $this->connection->query($query);
    if($result->num_rows > 0){
      $this->response["status"] = false;
      $this->response["message"] = "email address already used";
      return $this->response;
    }
    else{
      $password = password_hash($password,PASSWORD_DEFAULT);
      $query = "INSERT INTO users (username,email,password,admin,active,created,lastlogin,password_reset)
      VALUES ('$username','$email','$password',0,1,NOW(),NOW(),'$resetstring')";
      if($this->connection->query($query)){
        $this->response["status"] = true;
        $this->response["message"] = "account created";
      }
      else{
        $this->response["status"] = false;
        $this->response["message"] = "registration failed";
      }
      return $this->response;
    }
  }
  //authenticate user
  public function authenticate($email,$password){
    $email=filter_var($email,FILTER_SANITIZE_EMAIL);
    //check if email is valid after sanitization
    if(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
      $this->response["status"] = false;
      $this->response["message"] = "invalid email address";
      return $this->response;
    }
    $query = "SELECT username,email,password FROM users WHERE email='$email'";
    $result = $this->connection->query($query);
    if($result->num_rows == 1){
      $userdata = $result->fetch_assoc();
      $storedhash = $userdata["password"];
      $storedname = $userdata["username"];
      //verify the password
      if(password_verify($password,$storedhash)){
        //password matches
        $this->response["status"] = true;
        $this->response["message"] = "login successful";
        $this->isauth = true;
        //check if admin
        //create session
        
        //log authentication in database
      }
      else{
        //auth fail
        $this->response["status"] = false;
        $this->response["message"] = "email or password incorrect";
        $this->isauth = false;
      }
      return $this->response;
    }
    else{
      //there are more than one user with the same email (edge case)
    }
  }
}
?>