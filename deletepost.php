<?php
require_once("./common.php");
html_open("Delete User Posts");
print '<form method="post" action="./deleteposthandler.php">';
print '<table><thead><tr><th>Author</th><th>Content</th><th>Delete</th></tr></thead>';
print '<tbody>';
create_delete_view();
print '</tbody>';
print '</table>';
print '<p><input type="submit" value="Submit"></p>';
print '</form>';
html_close(true);
