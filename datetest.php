<?php 

$product = "Lagavulin 16 Year Old";
$query = "L";
if(preg_match("/{$query}/i", $product)) {
    echo 'true';
}
?>