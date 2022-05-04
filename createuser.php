<?php
/**
 * Reqs: 
 *  Store user if:
 *      User has content and does not match an existing user in the database
 *  Indicate whether or not the user was stored.
 */

require_once("./common.php");

$valid = (isset($_POST["user"]));
$user = "--";
if ($valid)
    $user = $_POST["user"];

if (strlen($user) == 0)
{
    $user = "--";
    $valid = false;
}
// $user = ($valid) ? $_POST["user"] : "--";

// function create_user( $user )
// {
//     $query = queries["users"]["create"];
// }





html_open("Create User " . $user);

var_dump($_POST);

if ($valid){
    print "<p>Creating user: <kbd>" . $user . "</kbd></p>";     
    if (create_user($user))
    {
        create_user($user);
        print "<p>User created successfully.</p>";
    } else {
        print "<p>Unable to create user <kbd>".$user."</kbd>: User already exists.</p>";
    }
} else {
    print "<p>Error: Could not create user. <br/>" .
          "Non-empty user argument (required) was not provided.</p>";
}

html_close();
