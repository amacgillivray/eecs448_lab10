<?php
require_once("./common.php");
html_open("Exercise 01 - Create Tables");
?>
        <h1>Configured with Lab10.sql</h1>
        <h2>SQL used to generate table:</h2>
        <pre>
            <?php print( file_get_contents("./lab10.sql") ); ?>
        </pre>
<?php html_close(); ?>