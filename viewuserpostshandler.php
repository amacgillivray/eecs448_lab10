<?php
require_once("./common.php");

## 
# GETTING POST ARGUMENTS
##
$valid = (isset($_POST["user"]) && strlen($_POST["user"]) > 0);
$user = ($valid) ? $_POST["user"] : "--";

##
# BUILDING PAGE 
## 
html_open("Viewing $user's Posts");
view_user_posts($user);
html_close(true);
