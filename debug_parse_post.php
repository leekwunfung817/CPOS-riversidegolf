<?php
// debug_parse_post.php - simulate visibility POST parsing
parse_str('visibility[zh][0][item][0]=1&visibility[zh][0][item][1]=0&visibility[zh][0][item][2]=1', $_POST);
var_export($_POST);
echo PHP_EOL;
?>