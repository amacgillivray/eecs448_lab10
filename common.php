<?php 

/**
 * @brief declares const "pass" with the password to the database;
 *        stored in home folder (outside web root) for security
 */
require_once("/home/a637m351/eecs448lab10pw.php");
const debug = 1;
const host = "mysql.eecs.ku.edu";
const user = "a637m351";
const db   = user;
const errmode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

const query_modes = [
    "create",
    "delete",
    "view",
];

// const prm_structure = [
//     "type",
//     "maxlength",
//     "nullable"
// ];
// const queries = [
//     "users" => [
//         "create" => [
//             "call" => "{CALL adduser (?)}",
//             "prm0" => [
//                 ""
//             ]
//         ],  
//         "view"   => "{CALL viewusers (?)}"
//     ],
//     "posts" => [
//         "create" => "{CALL addpost (?,?)}",
//         "delete" => "{CALL removepost(?)}",
//         "view"   => "{CALL viewuserposts(?)}"
//     ]
// ];


const queries = [
    "users" => [
        "create" => "CALL " . db . ".adduser(?)",
        "view"   => "CALL " . db . ".viewusers()"
    ],
    "posts" => [
        "create" => "CALL " . db . ".addpost(?,?)",
        "delete" => "CALL " . db . ".removepost(?)",
        "view"   => "CALL " . db . ".viewuserposts(?)"
    ]
];

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
        printf("Error occurred while reading sql query from $script.");
        return false;
    }

    // mysqli_report(errmode);
    $sql = new mysqli(host, user, pass);
    if ($sql->connect_errno)
    {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        return false;
    }

    if ($result = $sql->query($query))
    {
        $result->free();
    }

    $sql->close();
    return true;
}

function call_procedure( 
    $query, 
    $ptype = [],
    $parms = [],
    $cbparms = [],
    $cb = null,
    $sql = null
) {
    if (debug) print "<pre>";
    if (debug) print "FN: call_procedure ($query)\n";
    
    if ($sql === null) 
    {
        // mysqli_report(errmode);
        $sql = new mysqli(host, user, pass);
        if ($sql->connect_errno)
        {
            if (debug) printf("Connect failed: %s\n", $mysqli->connect_error);
            return false;
        }
    }
    
    $stmt = mysqli_stmt_init($sql);
    mysqli_stmt_prepare($stmt, $query);
        if (debug) print "Prepared statement.\n";

    for ($i = 0; $i < sizeof($parms); $i++)
    {
        $type = (isset($ptype[$i])) ? $ptype[$i] : "s";
        mysqli_stmt_bind_param(
            $stmt,
            $type,
            $parms[$i]
        );
        if (debug) print "Bound parameter $i: $parms[$i].\n";
    }
    
    if (debug) print "</pre>";
    
    if ($cb != null)
    {
        mysqli_stmt_execute($stmt);
            if (debug) print "Executed.\n";
            if (debug) var_dump($stmt);
        // $parameters = [$sql, $stmt];
        // todo - build the array with several conditions, then unpack in cb args
        return $cb($sql, $stmt, ...$cbparms);
    } else {
        $res = mysqli_stmt_execute($stmt);
                if (debug) print "Executed.\n";
                if (debug) var_dump($stmt);
        $sql->close();
        return $res;
    }
}

function create_user( $user )
{
    if (!user_exists($user))
        return call_procedure(
            queries["users"]["create"],
            ["s"],
            [$user]
        );
    else 
        return false;
}


function user_exists( $user )
{
    return call_procedure(
        queries["users"]["view"],
        ["s"],
        [$user],
        [$user],
        "user_exists_cb"
    );
}

function user_exists_cb($sql, $stmt, $user)
{
    $user_exists = false;
    while (!$user_exists)
    {
        $r = mysqli_stmt_get_result($stmt);
        if (debug) var_dump($r);
        
        $ru = mysqli_fetch_row($r)[0];
        if (debug) print "\nChecking user: " . $ru . "\n";
    
        if ($r === false)
            break;
        if ($ru == $user)
            $user_exists = true;
    }
    
    if (debug) print "Evaluated all results.\n";
    if (debug) print "</pre>";
    
    $sql->close();
    return $user_exists;
}

function create_post(
    
) {
    return true;
}

function html_open( $title )
{
    print 
    '<!DOCTYPE html>
    <html>
        <head>
            <title>' . $title . '</title>
            <link rel="stylesheet" href="./style.css">
        </head>
        <body>';
}

function html_close()
{
    print '</body></html>';
}
