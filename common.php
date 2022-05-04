<?php 

/**
 * @brief declares const "pass" with the password to the database;
 *        stored in home folder (outside web root) for security
 */
require_once("/home/a637m351/eecs448lab10pw.php");

/**
 * @const debug
 * @brief Toggles debugging statements. Note that while prepared statements
 *        protect from sql injection, enabling debugging may print untrusted 
 *        user input to the page and break the code. Thus, this value should 
 *        ABSOLUTELY be set to false in release.
 */
const debug = 1;

/**
 * @const host
 * @brief SQL host
 */
const host = "mysql.eecs.ku.edu";

/**
 * @const user
 * @brief SQL user
 */
const user = "a637m351";

/**
 * @const db
 * @brief Name of the SQL db to use.
 */
const db   = user;

/**
 * @const errmode
 * @brief mysqli error flags to use when initializing connections
 *        in common functions.
 */
const errmode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

const query_modes = [
    "create",
    "delete",
    "view",
];

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

/**
 * @brief Provides the boilerplate code to handle initializing an sql 
 *        connection and calling a stored procedure, using mysqli. Can be given
 *        a callback function (and parameters) to handle result sets. 
 * @param string $query
 *        The SQL statement that will call the desired SQL stored procedure.
 * @param array  $ptype   [ = [] ]
 *        A list of parameter types pertaining to each parameter that will be 
 *        bound to the prepared statement calling the procedure.
 *        Where no value is specified, "s" (string) will be assumed.
 *        - 
 *        Entries must be numerically indexed.
 *        - 
 *        Valid entries:
 *          i -- integer
 *          d -- double
 *          s -- string
 *          b -- blob (will be sent in packets)
 *        - 
 *        No checking is done for validity of the arguments. Providing values 
 *        other than i, d, s, or b may result in unknown behavior or errors.
 *        -
 *        See https://www.php.net/manual/en/mysqli-stmt.bind-param.php
 * @param array  $parms   [ = [] ]
 *        The list of parameters, in order, that should be bound to the stored 
 *        procedure call. The types of each argument should be indicated in
 *        $ptype. The length of the array should match the number of fields 
 *        in the stored procedure call.
 * @param array  $cbparms [ = [] ]
 *        Array containing parameters to be passed to the callback function.
 *        The array will be unpacked into individual arguments, passed in the 
 *        order they are provided (and preceded by $sql and $stmt). An empty 
 *        array has no effect.
 * @param string|null $cb [ = null ]
 *        String that is the fully-qualified name of the callback function.
 *        When not null, call_procedure() returns by calling this function
 *        immediately after statement execution with the following arguments:
 *          - $sql (the mysqli object)
 *          - $stmt (the mysqli statement (procedural) where the procedure 
 *             was executed. Contains the results of the query.)
 *          - ...$parms, if $fwparms = true 
 *          - ...$cbparms
 *        When null, the value returned by mysqli_stmt_execute will be returned
 *        instead (see https://www.php.net/manual/en/mysqli-stmt.execute.php).
 * @param bool $fwparms   [ = false ]
 *        Whether or not the parameters passed to the procedure should be 
 *        provided to the callback as well. When set to true, they will be 
 *        unpacked as parameters immediately before $cbparms.
 * @param object $sql     [ = null ]
 *        If desired, an existing mysqli object can be passed rather than 
 *        being initialized in the function.
 */
function call_procedure( 
    $query, 
    $ptype = [],
    $parms = [],
    $cbparms = [],
    $cb = null,
    $fwparms = false,
    $sql = null
) {
    if (debug) print "<pre>";
    if (debug) print "FN: call_procedure ($query)\n";
    
    if ($sql === null) 
    {
        mysqli_report(errmode);
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

    $typestring = "";
    for ($i = 0; $i < sizeof($parms); $i++)
        $typestring .= (isset($ptype[$i])) ? $ptype[$i] : "s";
    mysqli_stmt_bind_param(
        $stmt,
        $typestring,
        ...$parms
    );
    
    if ($cb != null)
    {
        mysqli_stmt_execute($stmt);
            if (debug) print "Executed.\n";
            // if (debug) var_dump($stmt);
        if (debug) print "</pre>";
        if ($fwparms) $cbparms = array_merge( $parms, $cbparms );
        return $cb($sql, $stmt, ...$cbparms);
    } else {
        print "Executing...";
        $res = mysqli_stmt_execute($stmt);
                if (debug) print "Executed.\n";
                // if (debug) var_dump($stmt);
        if (debug) print "</pre>";
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

function create_post( $user, $text )
{
    if (user_exists($user)) {
        return call_procedure(
            queries["posts"]["create"],
            [],
            [$user, $text]
        );
    } else {
        return false;
    }
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
    
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
            {
                if ($crawl[$i][0] == $user)
                {
                    $user_exists = true;
                    break;
                }
            }
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
    
    if (debug) print "Evaluated all results.\n";
    if (debug) print "</pre>";
    
    mysqli_stmt_free_result($stmt);
    $sql->close();
    return $user_exists;
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
    print '<footer>
           <a href="./index.html">&laquo; Back to Index</a>
           </footer>';
    print '</body></html>';
}
