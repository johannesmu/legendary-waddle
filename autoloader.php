<?php
spl_autoload_register(loadClass);

function loadClass($classname){
  $file = "classes/".strtolower($classname).".php";
  
  if(is_readable($file)){
    require_once($file);
  }
}

?>