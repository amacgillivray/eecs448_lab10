<?php
/**
 * @file createuser.php
 * @brief Form handler for createuser.html. 
 *
 * @details
 * Reqs: 
 *  Store user if:
 *      User has content and does not match an existing user in the database
 *  Indicate whether or not the user was stored.
 */

require_once("./common.php");

## 
# GETTING POST ARGUMENTS
##
$valid = (isset($_POST["user"]) && strlen($_POST["user"]) > 0);
$user = ($valid) ? $_POST["user"] : "--";

## 
# BUILDING THE PAGE
##
html_open("Create User " . $user);
if ($valid){
    # Describe the action that is being performed
    print "<p>Creating user: <kbd>" . $user . "</kbd></p>";     
    
    # Describe the result of the action
    if (create_user($user))
        print "<p>User created successfully.</p>";
    else 
        print "<p>Unable to create user <kbd>".$user."</kbd>: User already exists.</p>";
} else {
    # If the argument was invalid, inform the user
    print "<p>Error: Could not create user. <br/>" .
          "Non-empty user argument (required) was not provided.</p>";
}
html_close();
