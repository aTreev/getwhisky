<?php 
require_once("../category-attribute-list.class.php");
require_once("../util.class.php");
$page = new Page(3);
if ($page->getUser()->getUsertype() != 3) {
    echo json_encode(0);
    exit();
    die();
    return;
}

if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch ($functionToCall) {
        case 1:
            getCategoryManagementAttributeList();
        break;
        case 2:
            uploadNewCategoryAttribute();
        break;
        case 3:
            deleteCategoryAttribute();
        break;
        case 4:
            deleteAttributeValue();
        break;
        case 5:
            uploadNewAttributeValue();
        break;
        
    }
}


function getCategoryManagementAttributeList() {
    if (!util::valInt($_POST['categoryid'])) {
        echo json_encode(['result' => 0, 'html' => ""]); 
        return;
    }

    $categoryid = util::sanInt($_POST['categoryid']);
    $attributeList = new CategoryAttributeList($categoryid);
    echo json_encode($attributeList->displayAttributeListCategoryManagementPage());
}

function uploadNewCategoryAttribute() {
    if (!util::valInt($_POST['categoryid']) || !util::valStr($_POST['newAttributeTitle'])) {
        echo json_encode(['result' => 0, 'message' => 'Something went wrong, please refresh and try again']);
        return;
    }

    $categoryid = util::sanInt($_POST['categoryid']);
    $newAttributeTitle = util::sanStr($_POST['newAttributeTitle']);
    $messages = "";
    $insert = new MenuCRUD();

    $result = $insert->createCategoryAttribute($categoryid, $newAttributeTitle);
    if ($result == 1) $messages = "<b>".$newAttributeTitle . "</b> successfully added to filter list";
    else $messages = "An error occurred when trying to add the filter, please try again";

    echo json_encode(['result' => $result, 'message' => $messages]);
}

function deleteCategoryAttribute() {
    if (!util::valInt($_POST['categoryid']) || !util::valInt($_POST['attributeid'])) {
        echo json_encode(['result' => 0, 'message' => 'Something went wrong, please refresh and try again']);
        return;
    }

    $categoryid = util::sanInt($_POST['categoryid']);
    $attributeid = util::sanInt($_POST['attributeid']);
    $delete = new MenuCRUD();

    $result = $delete->deleteCategoryAttribute($categoryid, $attributeid);
    
    if ($result == 1) $messages = "Category filter deleted successfully";
    else $messages = "An error occurred when trying to delete the filter, please try again";
    
    echo json_encode(['result' => $result, 'message' => $messages]);

}

function deleteAttributeValue() {
    if (!util::valInt($_POST['attributeid']) || !util::valInt($_POST['attributeValueId'])) {
        echo json_encode(['result' => 0, 'message' => 'Something went wrong, please refresh and try again']);
        return;
    }

    $attributeid = util::sanInt($_POST['attributeid']);
    $attributevalueid = util::sanInt($_POST['attributeValueId']);
    $delete = new MenuCRUD();

    $result = $delete->deleteAttributeValue($attributeid, $attributevalueid);
    
    if ($result == 1) $messages = "filter option deleted successfully";
    else $messages = "An error occurred when trying to delete the filter option, please try again";
    
    echo json_encode(['result' => $result, 'message' => $messages]);

}

function uploadNewAttributeValue() {
    if (!util::valInt($_POST['attributeid']) || !util::valStr($_POST['attributeValue'])) {
        echo json_encode(['result' => 0, 'message' => 'Something went wrong, please refresh and try again']);
        return;
    }

    $attributeid = util::sanInt($_POST['attributeid']);
    $attributeValue = util::sanStr($_POST['attributeValue']);
    $messages = "";
    $insert = new MenuCRUD();

    $result = $insert->createAttributeValue($attributeid, $attributeValue);
    if ($result == 1) $messages = "<b>".$attributeValue."</b> successfully added to filter option list";
    else $messages = "An error occurred when trying to add the filter option, please try again";

    echo json_encode(['result' => $result, 'message' => $messages]);
}
?>