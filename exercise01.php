<?php
require_once("./common.php");
html_open("Exercise 01 - Create Tables");
?>
        <h1>Running Lab10.sql</h1>
        <?php 
        print "<p>";
        if (configure_db( "./lab10.sql" )); 
            print "Script ran successfully.";
        print "</p>";
        ?>
        <h2>SQL used to generate table:</h2>
        <pre>
            <?php print( file_get_contents("./lab10.sql") ); ?>
        </pre>
<?php html_close(); ?>