<?php
include("includes/session.php");
include("includes/dbconnection.php");
//check for POST variables
if(count($_GET)>0){
    $search_query = filter_var($_GET["search"],FILTER_SANITIZE_STRING);
    $dbquery = "SELECT * FROM products 
    WHERE name LIKE '%$search_query%'
    OR description LIKE '%$search_query%'";
    $result = $dbconnection->query($dbquery);
    $search_result = array();
    if($result->num_rows > 0){
        while($row=$result->fetch_assoc()){
            array_push($search_result,$row);
        }
    }
}
?>
<!doctype html>
<html>
   <?php include("includes/head.php");?>
   <body>
       <?php include("includes/navigation.php");?>
       <div class="container">
           <div class="row">
               <div class="col-md-12">
                   <h4>Search Results</h4>
                   <?php
                   echo "You searched for <strong>".$search_query."</strong>";
                   ?>
               </div>
           </div>
           <div class="row">
               <?php
               if(count($search_result)>0){
                   foreach($search_result as $item){
                       $id = $item["id"];
                       $name = $item["name"];
                       $description = $item["description"];
                       $image = $item["image"];
                       echo 
                       "<div class=\"col-md-3\">
                        <h3>$name</h3>
                        <img class=\"img-responsive\" src=\"images/$image\">
                        <p>$description</p>
                        <a class=\"btn btn-default\" href=\"detail.php?id=$id\">Detail</a>
                       </div>";
                   }
               }
               else{
                   echo "<div class=\"col-md-12 alert\">
                   <p>Your search for $search_query returned no result</p>
                   </div>";
               }
               ?>
           </div>
       </div>
       <?php include("includes/scripts.php");?>
   </body>
</html>