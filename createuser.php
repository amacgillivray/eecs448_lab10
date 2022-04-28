<?php
require_once("common.php");

/**
 * Reqs: 
 *  Store user if:
 *      User has content and does not match an existing user in the database
 *  Indicate whether or not the user was stored.
 */

$stored = false;

if (sizeof($_POST["user"]) == 0)
{

}