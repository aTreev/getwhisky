<?php 
require_once("../util.class.php");
require_once("../usercrud.class.php");
require_once("../unique-id-generator.class.php");
if (util::valStr($_POST['email'])) {
    $email = util::sanStr($_POST['email']);

    $userCRUD = new Usercrud();

    $found = $userCRUD->getUserByEmail($email);
    if ($found) {
        // generate unique password reset key
        
        $uniqueIdGenerator = new UniqueIdGenerator("passwordResetKey");
        $resetKey = $uniqueIdGenerator->getUniqueId();
        $userCRUD->setResetKeyByEmail($resetKey, $email);
        // send email with reset key
        $subject = "getwhisky password reset";
        $message = "<p>You have requested a password reset, please follow the following link to reset your password.</p><a href='http://ecommercev2/reset-password.php?resetKey=$resetKey'>reset password</a>";
        $headers = "From: neilunidev@yahoo.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        mail($email, $subject, $message, $headers);
        echo json_encode(1);
    } else {
        echo json_encode(0);
    }
    
}

?>