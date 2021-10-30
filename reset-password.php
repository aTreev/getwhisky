<?php 
    require_once("php/util.class.php");
    if (util::valStr($_GET['resetKey'])) {
        $resetKey = util::sanStr($_GET['resetKey']);
        echo $resetKey;
    }
?>