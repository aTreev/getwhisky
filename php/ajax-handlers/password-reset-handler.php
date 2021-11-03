<?php 
require_once("../util.class.php");
require_once("../page.class.php");
/****************
 * File ensures the email is a valid string
 * and sends the email to the page class for
 * further validation
 ************************************/
if (util::valStr($_POST['email'])) {
    $email = util::sanStr($_POST['email']);
    $page = new Page();
    $result = $page->sendPasswordResetEmail($email);
    echo json_encode($result);
}

?>