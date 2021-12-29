<?php 
require_once("../page.class.php");
require_once("../menucrud.class.php");
if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch($functionToCall) {
        case 1:
            createProductAttributeSelection();
        break;
        case 2:
        break;
    }
}


function createProductAttributeSelection() {
    if (isset($_POST['categoryid']) && util::valInt($_POST['categoryid'])) {
        $categoryId = util::sanInt($_POST['categoryid']);
        $source = new MenuCRUD();
        $html = "";
        $result = 0;
        $attributeList = $source->getProductFiltersByCategoryId($categoryId);

        // This is devolving into chaos
        if (!empty($attributeList)) {
            $result = 1;
            $html.="<div class='form-header' style='margin-top:20px;'><h4>Product Attribute selection</h4><p>The attributes selected below will be displayed on the product page as product details and will also be used for selection through product filters</p></div>";
            $html.="";
            foreach($attributeList as $attribute) {
                $attrValues = $source->getAttributeValuesByAttributeId($attribute['id']);
                $html.="<div class='input-container-100'>";
                    $html.="<label for='product-attribute'>".$attribute['title']."</label>";
                    $html.="<select class='form-item' attribute-id='".$attribute['id']."' name='product-attribute'>";
                        $html.="<option value='-1'>Please select an option</option>";
                        foreach($attrValues as $attrValue) {
                            $html.="<option value='".$attrValue['id']."' >".$attrValue['value']."</option>";
                        }
                    $html.="</select>";
                $html.="</div>";
            }
        }
        $result = ['result' => $result, 'html' => $html];
        echo json_encode($result);
    }
}
?>