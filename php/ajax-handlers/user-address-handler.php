<?php 
require_once("../page.class.php");

if(isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch ($functionToCall) {
        case 1:
            addNewAddress();
        break;

        case 2:
            updateUserAddress();
        break;

        case 3:
            deleteUserAddress();
        break;
    }
}


/****************
 * Adds an address to the database through the user.class.php file
 * Performs basic validation to ensure fields arent malicious etc
 */
function addNewAddress() {
    if (util::valStr($_POST['identifier'], array(1,50)) && util::valStr($_POST['fullName'], array(1,90)) && util::valStr($_POST['postcode'], array(5,10)) && util::valStr($_POST['line1'], array(1,80)) && util::valStr($_POST['city'], array(1,50))) {
        $page = new Page();
        // required fields
        $identifier = util::sanStr($_POST['identifier']);
        $fullName = util::sanStr($_POST['fullName']);
        $postcode = util::sanStr($_POST['postcode']);
        $line1 = util::sanStr($_POST['line1']);
        $city = util::sanStr($_POST['city']);
        // Optional fields
        $phoneNumber = util::sanStr($_POST['phoneNumber']);
        $line2 = util::sanStr($_POST['line2']);
        $county = util::sanStr($_POST['county']);

        $uniqueIdGenerator = new UniqueIdGenerator("address_id");
        $addressId = $uniqueIdGenerator->getUniqueId();

        $result = $page->getUser()->addNewAddress($addressId, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county);
        $html = $page->getUser()->getAndDisplayAddressPage();

        $result = ['result' => $result, 'html' => $html];

        echo json_encode($result);
        // Extra validation of fields
    } else {
        echo json_encode(util::valStr($_POST['identifier'], array(0,50)));
    }
}

function updateUserAddress() {
    if (util::valStr($_POST['addressId']) && util::valStr($_POST['identifier'], array(1,50)) && util::valStr($_POST['fullName'], array(1,90)) && util::valStr($_POST['postcode'], array(5,10)) && util::valStr($_POST['line1'], array(1,80)) && util::valStr($_POST['city'], array(1,50))) {
        $page = new Page();
        // required fields
        $addressId = util::sanStr($_POST['addressId']);
        $identifier = util::sanStr($_POST['identifier']);
        $fullName = util::sanStr($_POST['fullName']);
        $postcode = util::sanStr($_POST['postcode']);
        $line1 = util::sanStr($_POST['line1']);
        $city = util::sanStr($_POST['city']);
        // Optional fields
        $phoneNumber = util::sanStr($_POST['phoneNumber']);
        $line2 = util::sanStr($_POST['line2']);
        $county = util::sanStr($_POST['county']);

        $result = $page->getUser()->updateUserAddress($addressId, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county);

        $result = ['result' => $result, 'html' => $page->getUser()->getAndDisplayAddressPage()];

        echo json_encode($result);
    }
}

function deleteUserAddress() {
    if (util::valStr($_POST['addressId'])) {
        $page = new Page();
        $addressId = util::sanStr($_POST['addressId']);

        $result = $page->getUser()->deleteUserAddress($addressId);
        $result = ['result' => $result, 'html' => $page->getUser()->getAndDisplayAddressPage()];

        echo json_encode($result);
        exit();
    }
}
?>