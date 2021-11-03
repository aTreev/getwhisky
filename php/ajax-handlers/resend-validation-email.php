<?php
try {
    require_once("../page.class.php");
    $page = new Page(0);
    // send verification email
    $result = $page->resendValidationEmail();
    echo json_encode($result);
} catch(Exception $e) {
    echo json_encode($e);
}
    