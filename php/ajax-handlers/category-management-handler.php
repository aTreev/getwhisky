<?php 
require_once("../category-attribute-list.class.php");
if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch ($functionToCall) {
        case 1:
            getCategoryManagementAttributeList();
        break;
        
    }
}
?>