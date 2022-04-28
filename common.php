<?php 

/**
 * @brief declares const "pass" with the password to the database;
 *        stored in home folder (outside web root) for security
 */
require_once("/home/a637m351/eecs448lab10pw.php");
const host = "mysql.eecs.ku.edu";
const user = "a637m351";

/**
 * @brief Uses exercise01.sql to configure the database with the user and post tables.
 */
function configure_db( $script )
{
    $result = "";
    $query = file_get_contents( $script );
    if ($query === false)
    {
        // error occured during file read
        die("Error occurred while reading sql query from $script.");
    }

    $sql = new mysqli(host, user, pass);
    if ($sql->connect_errno)
    {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    if ($result = $sql->query($query))
    {
        $result->free();
    }

    $sql->close();
}
