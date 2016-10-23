<?php
class Database{
  private $database;
  private $connection;
  private $user;
  private $password;
  private $status;
  private $response = array();
  public function __construct($host,$db){
    $this->database = $db;
    $this->host = $host;
    $this->user = getenv('dbuser');
    $this->password = getenv('dbpassword');
    $this->connection = mysqli_connect(
      $this->host,
      $this->user,
      $this->password,
      $this->database
    );
    if($this->connection){
      $this->status=true;
    }
    else{
      $this->status=false;
    }
    //return $this->connection;
  }
  public function getConnection(){
    return $this->connection;
  }
  private function toString(){
    return (string)$this->status;
  }
  public function getStatus(){
    return $this->status;
  }
}
?>