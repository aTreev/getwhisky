<?php 
require_once("../page.class.php");
require_once("../category-attribute-list.class.php");
$page = new Page(3);
if ($page->getUser()->getUsertype() != 3) {
    echo json_encode(0);
    exit();
    die();
    return;
}

if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch($functionToCall) {
        case 1:
            getCategoryAttributeList();
        break;
        case 2:
            uploadCreatedProduct();
        break;
    }
}


/***********
 * Takes a posted categoryid and retrieves the attribute list for that category
 * using the ProductAttributeList class
 * returns an assoc array ['result', 'html']
 ******/
function getCategoryAttributeList() {
    if (isset($_POST['categoryid']) && util::valInt($_POST['categoryid'])) {
        $categoryid = util::sanInt($_POST['categoryid']);
        $attributeList = new CategoryAttributeList($categoryid);
        $attributeList = $attributeList->displayCategoryAttributeList();

        echo json_encode($attributeList);
    }
}


function uploadCreatedProduct() {
    if (util::valStr($_POST['productName']) && util::valStr($_POST['productType']) && util::valFloat($_POST['productPrice']) && util::valInt($_POST['productStock']) && util::valStr($_POST['productDesc']) && util::valImage($_FILES['productImage']['tmp_name']) && util::valInt($_POST['categoryid'])) {
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

        // Product uploaded successfully get product ID
        $productid = $productCRUD->getLastCreatedProductId()[0]['id'];

        // Handle attribute logic if passed
        if (util::posted(json_decode($_POST['attributeValueIds']))) createProductAttributes($productCRUD, $productid);

        // Handle product overview logic if passed
        if (util::posted(json_decode($_POST['overviewTitles']))) createProductOverviews($productCRUD, $productid);
        

        echo json_encode(['result' => 1, 'message' => "Product ".$name." uploaded successfully! <a href='/productpage.php?pid=".$productid."'>View Product</a>"]);
    } 
    
}

/************
 * Retrieves the product attributeValueId POST data and formats it to an array
 * Attempts to upload attributes to the database
 *************/
function createProductAttributes($productCRUD, $productid) {
    $attributeValueIds = [];
    
    foreach(json_decode($_POST['attributeValueIds']) as $attributeValueId) {
        array_push($attributeValueIds, util::sanInt($attributeValueId));
    }

    // Upload each attribute
    foreach($attributeValueIds as $attributeValueId) {
        $productCRUD->createProductAttribute($attributeValueId, $productid);
    }
}

/*******
 * Creates and uploads a product overview to the database
 * Code is horrible and cluttered due to the structure of the
 * $_FILES variable
 * Function works by detecting whether an image has been provided alongside
 * The rest of the overview item by checking the $imageIndex 0=no file 1=file
 * If the file is invalid or fails to upload the overview item is uploaded without
 * the file
 ****************/
function createProductOverviews($productCRUD, $productid) {
    $overviewImageIndex = [];
    $overviewTitles = [];
    $overviewTexts = [];

    // Get decoded arrays and sanitize
    foreach(json_decode($_POST['overviewPostIndex']) as $overviewPostIndex) {
        array_push($overviewImageIndex, util::sanInt($overviewPostIndex));
    }
    foreach(json_decode($_POST['overviewTitles']) as $overviewTitle) {
        array_push($overviewTitles, util::sanStr($overviewTitle));
    }
    foreach(json_decode($_POST['overviewTexts']) as $overviewText) {
        array_push($overviewTexts, util::sanStr($overviewText));
    }
    
    // Loop through the index to find the number of overview items passed
    foreach($overviewImageIndex as $imageIndex) {
        // Image index == 0 no image uploaded
        if ($imageIndex == 0) {
            $productCRUD->createProductOverview($productid, null, $overviewTitles[0], $overviewTexts[0]);
        }

        // Image index == 1 image uploaded
        if ($imageIndex == 1) {
            // If invalid file type or file size too big upload without image
            if (!util::valImage($_FILES['overviewImages']['tmp_name'][0]) || !util::valFileSize($_FILES['overviewImages']['size'][0])) {
                $productCRUD->createProductOverview($productid, null, $overviewTitles[0], $overviewTexts[0]);
            } else {
                // Upload with image if image uploads to directory
                $imageFileName = "../../assets/product-overview-images/" .time().basename($_FILES["overviewImages"]["name"][0]);
                if (move_uploaded_file($_FILES["overviewImages"]["tmp_name"][0], $imageFileName)) {
                    $productCRUD->createProductOverview($productid, $imageFileName, $overviewTitles[0], $overviewTexts[0]);
                } else {
                    // Image upload failed upload overview without image
                    $productCRUD->createProductOverview($productid, null, $overviewTitles[0], $overviewTexts[0]);
                }
            }
            // If $_FILES is still array remove first element
            if (count($_FILES['overviewImages']['name']) > 1) {
                array_shift($_FILES['overviewImages']['error']);
                array_shift($_FILES['overviewImages']['name']);
                array_shift($_FILES['overviewImages']['size']);
                array_shift($_FILES['overviewImages']['tmp_name']);
                array_shift($_FILES['overviewImages']['type']);
            }
            
        }
        // Remove first element of titles and texts
        array_shift($overviewTitles);
        array_shift($overviewTexts);
    }
}



function uploadProductImageToSite($imageFile, $fileName) {
    if (!util::valFileSize($imageFile['size'])) {
        return ['result' => false, 'message' => "Image exceeds maximum filesize, please upload an image less than ".ini_get("upload_max_filesize")." "];
    }

    if(move_uploaded_file($imageFile['tmp_name'], $fileName)) {
        return ['result' => true];
    }
    return ['result' => false, 'message' => 'There was an error uploading your file, please try again'];
}
?>