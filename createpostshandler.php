<?php
/**
 * Reqs: 
 *  Store post if:
 *      Textarea has content
 *      User has content and matches a user in the database
 *  Indicate whether or not the post was stored.
 */

require_once("./common.php");

## 
# GETTING POST ARGUMENTS
##
$vu = ((isset($_POST["user"])) && (strlen($_POST["user"]) > 0)); # Valid user
$vp = ((isset($_POST["post"])) && (strlen($_POST["post"]) > 0)); # Valid post
# if valid user/post, get the user/post
$user = ($vu) ? $_POST["user"] : "";
$post = ($vp) ? $_POST["post"] : "";
# Update valid user bool - user value must match a record in db
$v = ($vp && $vu); # Valid args?

##
# BUILD THE PAGE 
##
html_open("Handling Post Creation");
if ($v)
{
    print "<h1>Creating post for $user</h1>";
    # Create the post
    if (create_post( $user, $post ))
    {
        print "<p>Post added successfully.</p>";
    } else {
        print "<p>Error: Provided username '$user' does not match any existing user.</p>";        
    }  
} else {
    # Indicate any issues with the vu / vp values 
    if (!$vu) 
        print "<p>Error: Provided username was empty or non-interpretable</p>";
    if (!$vp) 
        print "<p>Error: Provided post text is empty</p>";
}
html_close();
