<?php
try {
    require_once("../page.class.php");
    $page = new Page(0);
    // send verification email
    $emailTo = $page->getUser()->getEmail();
    $vKey = $page->getUser()->getVerificationKey();
    $subject = "JA Mackay email verification";
    $message = "<h1>Thank you for registering with JA Mackay</h1><p>Please click on the link below to verify your account!</p><a href='http://ecommercev2/verify.php?vkey=$vKey'>Verify account</a>";
    $headers = "From: neilunidev@yahoo.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $result['sent'] = mail($emailTo, $subject, $message, $headers);
    $result['address'] = $emailTo;
    echo json_encode($result);
} catch(Exception $e) {
    echo json_encode($e);
}
    