<?php
$result = scandir("images");
$files = array();
$count = 0;
foreach($result as $file){
    if(!is_dir($file)){
        $path = "images/".$file;
        $hash = md5_file($path);
        //echo $hash."&nbsp;".$count++." ".$file."<br>";
    }
}
echo apache_getenv('TEST');
echo getenv('TEST');
print_r($_ENV);
?>