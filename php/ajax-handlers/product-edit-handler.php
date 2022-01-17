<?php 
require_once("../page.class.php");
require_once("../category-attribute-list.class.php");
$page = new Page(3);
if ($page->getUser()->getUserType() != 3) {
    exit();
    die();
    return;
}
if (isset($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch($functionToCall) {
        case 1:
            getCategoryAttributeList();
        break;
        
        case 2:
            getProductAttributeData();
        break;

        case 3:
        break;

        case 4:
            updateProductImage();
        break;

        case 5:
            updateProductName();
        break;

        case 6:
            updateProductType();
        break;

        case 7:
            updateProductPrice();
        break;

        case 8:
            updateProductStock();
        break;

        case 9:
            updateProductDescription();
        break;

        case 10:
            updateProductAlcoholVolume();
        break;

        case 11:
            updateProductBottleSize();
        break;
        
        case 12:
            updateProductCategoryAndAttributes();
        break;

        case 13:
            updateProductOverviewSection();
        break;

        case 14:
            deleteProductOverviewSection();
        break;

        case 15:
            createProductOverviewSection();
        break;
    }
}

/***********
 * Takes a posted categoryid and retrieves the attribute list for that category
 * using the ProductAttributeList class
 * returns an assoc array ['result', 'html']
 ******/
function getCategoryAttributeList() {
    if (util::valInt($_POST['categoryid'])) {
        $categoryid = util::sanInt($_POST['categoryid']);
        $attributeList = new CategoryAttributeList($categoryid);
        $attributeList = $attributeList->displayCategoryAttributeList();

        echo json_encode($attributeList);
    }
}

/**********
 * Retrieves a product's attribute data (attribute_id / attribute_value_id) via the crud class
 * returns the following data:
 *  @result.result:     1: data retrieved | 0: no data retrieved
 *  @result.product_attribute_data: object array of [attribute_id, attribute_value_id]
 *********************/
function getProductAttributeData(){
    if (util::valInt($_POST['productid'])) {
        $productid = util::sanInt($_POST['productid']);
        $source = new ProductCRUD();
        $result = 0;

        $productAttributeData = $source->getProductAttrValIdsAndAttrIds($productid);

        if ($productAttributeData) {
            $result = 1;
        }
        echo json_encode(['result' => $result, 'product_attribute_data' => $productAttributeData]);
    }
}


function updateProductImage() {
    if (isset($_FILES['productImage']) && util::valInt($_POST['productid'])) {
        $update = new ProductCRUD();
        $image = $_FILES['productImage'];
        $productid = util::sanInt($_POST['productid']);
        $messages = "";
        $result = 0;

        if (!util::valImage($image['tmp_name'])) {
            $messages = "Please ensure an image has been uploaded";
        }
        if (!util::valFileSize($image['size'])) {
            $messages = "Please upload a file smaller than the maximum of ".ini_get('upload_max_filesize')."";
        }

        if ($messages == "") {
            $imageDest = "../../assets/product-images/" .time().basename($image["name"]);
            if (move_uploaded_file($image['tmp_name'],$imageDest)){
                $result = $update->updateProductImage($imageDest, $productid);
                if ($result == 1) {
                    $messages = "Product Image updated";
                } else {
                    $messages = "There was an error updating the product image, please try again";
                }
            } else {
                $messages = "There was an error uploading the file, please try again";
            }
        }

        echo json_encode(['result' => $result, 'message' => $messages]);
    }
}
function updateProductName() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['productName'])) {
        $update = new ProductCRUD();
        $name = util::sanStr($_POST['productName']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductName($name, $productid));
    }
}
function updateProductType() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['productType'])) {
        $update = new ProductCRUD();
        $type = util::sanStr($_POST['productType']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductType($type, $productid));
    }
}
function updateProductPrice() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['productPrice'])) {
        $update = new ProductCRUD();
        $price = util::sanStr($_POST['productPrice']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductPrice($price, $productid));
    }
}
function updateProductStock() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['productStock'])) {
        $update = new ProductCRUD();
        $stock = util::sanInt($_POST['productStock']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductStock($stock, $productid));
    }
}
function updateProductDescription() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['productDesc'])) {
        $update = new ProductCRUD();
        $desc = util::sanStr($_POST['productDesc']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductDescription($desc, $productid));
    }
}
function updateProductAlcoholVolume() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['alcVolume'])) {
        $update = new ProductCRUD();
        $alcVolume = util::sanStr($_POST['alcVolume']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductAlcoholVolume($alcVolume, $productid));
    }
}
function updateProductBottleSize() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['bottleSize'])) {
        $update = new ProductCRUD();
        $bottleSize = util::sanStr($_POST['bottleSize']);
        $productid = util::sanInt($_POST['productid']);
        echo json_encode($update->updateProductBottleSize($bottleSize, $productid));
    }
}

/*******
 * Updates a product's category and optionally its attributes
 * Currently has no way of detecting errors because simply checking
 * affected_rows isn't sufficient enough to throw an error (0 result)
 ****************/
function updateProductCategoryAndAttributes() {
    if (util::valInt($_POST['productid']) && util::valInt($_POST['categoryid'])) {
        $CRUD = new ProductCRUD();
        $productid = util::sanInt($_POST['productid']);
        $categoryid = util::sanInt($_POST['categoryid']);

        // Reset attributes
        $attributesReset = $CRUD->resetProductAttributes($productid);
        // Update category
        $categoryUpdated = $CRUD->updateProductCategory($categoryid, $productid);

        // Check if attribute value ids passed
        if (isset($_POST['attributeValueIds'])) {
            foreach(array_filter($_POST['attributeValueIds'], "ctype_digit") as $attributeValueId) {
                $CRUD->createProductAttribute(util::sanInt($attributeValueId), $productid);
            }
            echo json_encode(['result' => 1, 'message' => 'Product category and attributes updated successfully']);
            return;

        }
        echo json_encode(['result' => 1, 'message' => 'Product category updated successfully']);
    }
}

function updateProductOverviewSection() {
    if (util::valInt($_POST['productid']) && util::valInt($_POST['overviewid']) && util::valStr($_POST['overviewTitle']) && util::valStr($_POST['overviewText'])) {
        $productid = util::sanInt($_POST['productid']);
        $overviewid = util::sanInt($_POST['overviewid']);
        $overviewTitle = util::sanStr($_POST['overviewTitle']);
        $overviewText = util::sanStr($_POST['overviewText']);
        $imageDest = util::sanStr($_POST['currentImgSrc']);
        $result = 0;
        $messages = "";

        // Check for new uploaded file
        if (isset($_FILES['overviewImage'])) {
            $image = $_FILES['overviewImage'];
            if (!util::valImage($image['tmp_name'])) {
                $messages = "Please ensure the uploaded file is an image";
            }
            if (!util::valFileSize($image['size'])) {
                $messages = "Please upload a file smaller than the maximum of ".ini_get('upload_max_filesize')."";
            }

            if ($messages == "") {
                $imageDest = "../../assets/product-overview-images/" .time().basename($image["name"]);
                if (!move_uploaded_file($image['tmp_name'],$imageDest)){
                    $messages = "There was an error uploading the file, please try again";
                }
            }
        }

        // If no errors continue
        if ($messages == "") {
            $update = new ProductCRUD();
            $result = $update->updateProductOverview($imageDest, $overviewTitle, $overviewText, $overviewid, $productid);
            if ($result == 1) $messages = "Product overview successfully updated";
            else $messages = "There was an error updating the overview, please try again";
        }

        echo json_encode(['result' => $result, 'message' => $messages]);

    }
}

function deleteProductOverviewSection() {
    if (util::valInt($_POST['overviewid']) && util::valInt($_POST['productid'])) {
        $overviewid = util::sanInt($_POST['overviewid']);
        $productid = util::sanInt($_POST['productid']);
        $delete = new ProductCRUD();
        $messages = "";

        $result = $delete->deleteProductOverview($overviewid, $productid);

        if ($result == 1) $messages = "Product overview deleted successfully";
        else $messages = "There was an error deleteing the overview, please try again";

        echo json_encode(['result' => $result, 'message' => $messages]);
    }
}

function createProductOverviewSection() {
    if (util::valInt($_POST['productid']) && util::valStr($_POST['overviewTitle']) && util::valStr($_POST['overviewText'])) {
        $productid = util::sanInt($_POST['productid']);
        $overviewTitle = util::sanStr($_POST['overviewTitle']);
        $overviewText = util::sanStr($_POST['overviewText']);
        $imageDest = null;
        $result = 0;
        $messages = "";
        $createdId = -1;

        // Check for new uploaded file
        if (isset($_FILES['overviewImage'])) {
            $image = $_FILES['overviewImage'];
            if (!util::valImage($image['tmp_name'])) {
                $messages = "Please ensure the uploaded file is an image";
            }
            if (!util::valFileSize($image['size'])) {
                $messages = "Please upload a file smaller than the maximum of ".ini_get('upload_max_filesize')."";
            }

            if ($messages == "") {
                $imageDest = "../../assets/product-overview-images/" .time().basename($image["name"]);
                if (!move_uploaded_file($image['tmp_name'],$imageDest)){
                    $messages = "There was an error uploading the file, please try again";
                }
            }
        }

        // If no errors continue
        if ($messages == "") {
            $CRUD = new ProductCRUD();
            $result = $CRUD->createProductOverview($productid, $imageDest, $overviewTitle, $overviewText);

            if ($result == 1) {
                $messages = "Product overview created successfully";
                $createdId = $CRUD->getLastOverviewId()[0]['id'];
            } else {
                $messages = "There was an error adding the overview, please try again";
            }
        }

        echo json_encode(['result' => $result, 'message' => $messages, 'id' => $createdId]);
    }
}
?>