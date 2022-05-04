<?php
require_once("./common.php");
html_open("Create User");
?>
        <form method="post" action="./createuserhandler.php">
            <p><label for="user">Username</label><input id="user" name="user" type="text" value="" required/></p>
            <p><input type="submit" value="Submit"></p>
        </form>
<?php 
html_close();
?> 