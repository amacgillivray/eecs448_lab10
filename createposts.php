<?php
require_once("./common.php");
html_open("Create Post");
?>
        <form method="post" action="./createpostshandler.php">
            <p><label for="user">Username</label><input id="user" name="user" type="text" value="" required/></p>
            <textarea id="post" name="post" cols="80" rows="20"></textarea>
            <p><input type="submit" value="Submit"></p>
        </form>
<?php 
html_close();
?>