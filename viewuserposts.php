<?php
require_once("./common.php");
html_open("View User Posts");
print '<form method="post" action="./viewuserpostshandler.php">';
select_users();
print '<p><input type="submit" value="Submit"></p>';
print '</form>';
html_close(true);
