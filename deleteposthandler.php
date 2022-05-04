<?php
require_once("./common.php");

# Read from post
$clone = array_keys($_POST);

# Do action and build page
html_open("Deletion Handler");

$posts = [];
for ($i = 0; $i < sizeof($_POST); $i++)
{
    if (strpos($clone[$i], "del") === 0)
    {
        $id = $_POST[$clone[$i]];
        delete_post($id);
        print "<p>Deleted Post with ID #$id</p>"; 
    }
}

html_close(true);
