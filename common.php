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
    if (sizeof($parms) > 0)
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
        if (debug) print "Executing...";
        $res = mysqli_stmt_execute($stmt);
                if (debug) print "Executed.\n";
                // if (debug) var_dump($stmt);
        if (debug) print "</pre>";
        $sql->close();
        return $res;
    }
}

/**
 * @brief Creates a user
 * @param string $user 
 */
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

/**
 * @brief Creates a post
 * @param string $user
 * @param string $text
 */
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

/**
 * @brief  Checks if a user exists
 * @param  string $user
 * @return bool
 */
function user_exists( $user )
{
    return call_procedure(
        queries["users"]["view"],
        [],
        [],
        [$user],
        "user_exists_cb"
    );
}

/**
 * @brief Callback for user_exists; parses results of the query
 */
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
    
    $sql->close();
    return $user_exists;
}

/**
 * @brief Prints a <ul> list of all records in the users table
 */
function view_users()
{
    return call_procedure(
        queries["users"]["view"],
        [],
        [],
        [],
        "view_users_cb"
    );
}

/**
 * @brief Callback for view_users
 */
function view_users_cb($sql, $stmt)
{
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            print "<ul>";
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
                print "<li>" . $crawl[$i][0];
            print "</ul>";
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
}

/** 
 * @brief Prints a "select" / dropdown menu containing options for
 *        all of the records in the users table. 
 */
function select_users()
{
    return call_procedure(
        queries["users"]["view"],
        [],
        [],
        [],
        "select_users_cb"
    );
}

/**
 * @brief Callback for select_users
 */
function select_users_cb($sql, $stmt)
{
    print "<label for=\"user\">Select User</label>";
    print "<select name=\"user\" id=\"user\">";
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
                print     "<option value=\"" . $crawl[$i][0] . "\">" 
                        . $crawl[$i][0] 
                        . "</option>";
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
    print "</select>";
}

/**
 * @brief Creates a view with all posts from a given user
 */
function view_user_posts( $user )
{
    return call_procedure(
        queries["posts"]["view"],
        ["s"],
        [$user],
        [],
        "view_user_posts_cb"
    );
}

/** 
 * @brief Callback for view_user_posts
 */
function view_user_posts_cb($sql, $stmt)
{
    print   '<table>' .
            '<thead><th>ID</th><th>Content</th></thead><tbody>';
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
                print   "<tr>" .
                        "<td>".$crawl[$i][0]."</td>" .
                        "<td>".$crawl[$i][1]."</td>" .
                        "</tr>";
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
    print "</tbody></table>";
}

/** 
 * @brief Creates a view for deleting posts from any user.
 * @details 
 * Has two levels. First, gets a list of all users in the system 
 *  using delete_view_cb()
 * Then, delete_view_cb iterates over the list of users and calls
 *  the procedure to get posts from a user on each one. The posts 
 *  are parsed and printed in tabular format by delete_view_user_cb. 
 * Naming could be better but this will do for a lab. 
 * @see  call_procedure()
 * @uses delete_view_cb() via call_procedure()
 * @uses delete_view_user_cb() via call_procedure in delete_view_cb()
 */
function create_delete_view()
{
    return call_procedure(
        queries["users"]["view"],
        [],
        [],
        [],
        "delete_view_cb"
    );
}

/** 
 * @brief Callback for create_delete_view
 */
function delete_view_cb( $sql, $stmt )
{
    $users = [];
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
                $users[] = $crawl[$i][0];
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
    
    // var_dump($users);
    
    for ($i = 0; $i < sizeof($users); $i++)
    {
        $user = $users[$i];
        call_procedure(
            queries["posts"]["view"],
            ["s"],
            [$user],
            [$user],
            "delete_view_user_cb"
        );
    }
}

/** 
 * @brief Callback for create_delete_view (level 2) 
 */
function delete_view_user_cb( $sql, $stmt, $user )
{
    do {
        if ($result = mysqli_stmt_get_result($stmt))
        {
            $crawl = $result->fetch_all();
            for ($i = 0; $i < sizeof($crawl); $i++)
                print "<tr>" . 
                      "<td>$user</td>" .
                      "<td>" . $crawl[$i][1] . "</td>" .
                      "<td><input type=\"checkbox\" name=\"del" . $crawl[$i][0] . "\" value=\"" . $crawl[$i][0] . "\"></td>" .
                      "</tr>";
        }
    } while (mysqli_stmt_next_result($stmt) && !$user_exists);
}

/** 
 * @brief Deletes the post with the specified ID
 * @param int $post 
 *        The ID of the post to be deleted.
 */
function delete_post( $post )
{
    return call_procedure(
        queries["posts"]["delete"],
        ["i"],
        [$post]
    );
}

/** 
 * Prints opening HTML boilerplate with given title.
 */
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

/** 
 * Prints closing HTML boilerplate. When $admin is true, links to adminhome 
 *  instead of index.html 
 */
function html_close( $admin = false )
{
    print '<footer>';
    if ($admin) 
        print '<a href="./adminhome.html">&laquo; Back to Admin Home</a>';
    else
        print '<a href="./index.html">&laquo; Back to Index</a>';
    print '</footer></body></html>';
}
