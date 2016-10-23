<?php
//this class depends on database and user class
class Navigation{
  public $status;
  private $current;
  private $connection;
  public function __construct($db,$currentuser){
    //get current page
    $this->connection = $db->getConnection();
  }
}
?>