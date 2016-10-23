<?php
//generates a random set of characters and numbers
class StringGenerator{
  private $result;
  public function __construct($length){
    $this->result = bin2hex(openssl_random_pseudo_bytes($length));
  }
  public function getResult(){
    return $this->result;
  }
}
?>