<?php 
require_once("../page.class.php");
if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch ($functionToCall) {
        case 1:
            testUserPassword();
        break;
        case 2:
            updateUserDetails();
        break;
    }
}


function testUserPassword() {
    if (util::valStr($_POST['currentPassword'])) {
        $currentPassword = util::sanStr($_POST['currentPassword']);
        $page = new Page();

        $authenticated = $page->getUser()->testPassword($currentPassword);
        echo json_encode(['user_authenticated' => $authenticated]);
    }
}

function updateUserDetails() {
    $page = new Page();
    $firstName = $surname = $email = $password = "";
    if (util::valStr($_POST['firstName'])) $firstName = util::sanStr($_POST['firstName']);
    if (util::valStr($_POST['surname'])) $surname = util::sanStr($_POST['surname']);
    if (util::valEmail($_POST['email'])) $email = util::sanEmail($_POST['email']);
    if (util::valStr($_POST['newPassword'])) $password = util::sanStr($_POST['newPassword']);

    $result = $page->getUser()->updateUser($firstName, $surname, $password, $email, $usertype="", $page->getUser()->getUserid());

    echo json_encode($result);
}
?>