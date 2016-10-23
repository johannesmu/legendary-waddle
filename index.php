<?php
require("autoloader.php");

$db = new Database("localhost","test");

$user = new User($db);
$string = new StringGenerator(16);
$reset = $string->getResult();
echo "<p>$reset</p>";

echo "<a href=\"there/\">There</a>"

?>