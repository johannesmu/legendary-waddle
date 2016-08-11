<?php
include("includes/session.php");
unset($_SESSION["email"]);
session_destroy();
?>
<!doctype html>
<html>
    <?php include("includes/head.php");?>
    <body>
        <?php include("includes/navigation.php");?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p>Thank you for visiting</p>
                </div>
            </div>
        </div>
        <?php include("includes/scripts.php");?>
        <!--a javascript timer to show the home page after 3000ms-->
        <script>
            timer = setTimeout(function(){
                window.location.href="index.php";
            },3000);
        </script>
    </body>
</html>