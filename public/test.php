<?php
$hex = "#ff9900";
list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
echo "$hex -> $r $g $b";
?>