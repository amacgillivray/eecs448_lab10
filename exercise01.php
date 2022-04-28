<?php
require_once("./common.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Exercise 01 - Create Tables</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <?php 
        configure_db("lab10.sql"); 
        print("<p>Database successfully configured.</p>"); 
        ?>
    </body>
</html>