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
            uploadCreatedProduct();
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


function uploadCreatedProduct() {
    if (util::valStr($_POST['productName']) && util::valStr($_POST['productType']) && util::valFloat($_POST['productPrice']) && util::valInt($_POST['productStock']) && util::valStr($_POST['productDesc']) && util::valImage($_FILES['productImage']) && util::valInt($_POST['categoryid'])) {
        $productCRUD = new ProductCRUD();

        // Required values
        $name = util::sanStr($_POST['productName']);
        $type = util::sanStr($_POST['productType']);
        $price = $_POST['productPrice'];
        $stock = util::sanInt($_POST['productStock']);
        $desc = util::sanStr($_POST['productDesc']);
        $image = $_FILES['productImage'];
        $categoryid = util::sanInt($_POST['categoryid']);

        //Optional values
        $alcoholVolume = util::sanStr($_POST['alcoholVolume']);
        $bottleSize = util::sanStr($_POST['bottleSize']);
        
        // Construct image file name
        $imageFileName = "../../assets/product-images/" .time().basename($_FILES["productImage"]["name"]);

        // Attempt to upload file to site
        $fileUploaded = uploadProductImageToSite($image, $imageFileName);

        // Error uploading image, abort process and return error
        if (!$fileUploaded['result']) {
            echo json_encode(['result' => $fileUploaded['result'], 'message' => $fileUploaded['message']]);
            return;
        }

        // Error uploading product, return error
        if(!$productCRUD->createProduct($name, $desc, $imageFileName, $price, $stock, date('Y-m-d H:i:s'), $alcoholVolume, $bottleSize, $type, $categoryid)) {
            echo json_encode(['result' => 0, 'message' => "Error uploading product, please refresh and try again"]);
            return;
        }

    
        // Product uploaded successfully
        

        //TODO:
            // Implement upload of product attributes and product overviews

        // Handle attribute logic if passed
        if (util::valStr($_POST['attributeValueIds'])) {
            $productid = $productCRUD->getLastCreatedProductId()[0]['id'];
            $attributeValueIds = array_map('intval', explode(',', $_POST['attributeValueIds']));

            foreach($attributeValueIds as $attributeValueId) {
                $productCRUD->createProductAttribute($attributeValueId, $productid);
            }
        }

        echo json_encode(['result' => 1, 'message' => "Product ".$name." uploaded successfully!"]);
    } 
    
}


function uploadProductImageToSite($imageFile, $fileName) {
    if (!util::valFileSize($imageFile)) {
        return ['result' => false, 'message' => "Image exceeds maximum filesize, please upload an image less than ".ini_get("upload_max_filesize")." "];
    }

    if(move_uploaded_file($imageFile['tmp_name'], $fileName)) {
        return ['result' => true];
    }
    return ['result' => false, 'message' => 'There was an error uploading your file, please try again'];
}
?>