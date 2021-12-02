<?php 
require_once("../page.class.php");
$page = new Page(0);

if (util::valUname($_POST['username']) && util::valStr($_POST['firstname']) && util::valStr($_POST['surname']) && util::valEmail($_POST['email']) && util::valStr($_POST['userpass'])) {
    $uniqueIdGenerator = new UniqueIdGenerator("userid");
    $userid = $uniqueIdGenerator->getUniqueId();
    $uniqueIdGenerator = new UniqueIdGenerator("vkey");
    $vKey = $uniqueIdGenerator->getUniqueId();
    $username= util::sanStr($_POST['username']);
    $firstname= util::sanStr($_POST['firstname']);
    $surname= util::sanStr($_POST['surname']);
    $email= util::sanEmail($_POST['email']);
    $userpass= util::sanStr($_POST['userpass']);

    $result = $page->registerUser($userid,$username,$userpass,$firstname,$surname,$email, $vKey);
    echo json_encode($result);
} else {
    echo json_encode($result = ["insert" => 0]);
}
?>