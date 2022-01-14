<?php
require_once("php/page.class.php");
require_once("php/product-category-list.class.php");
$page = new Page(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
        $productCRUD = new ProductCRUD();
        $found = $page->setPageProductById(util::sanInt($_GET['pid']));
    ?>
    <link rel="stylesheet" href="style/css/product-page.css">
    <link rel="stylesheet" href="style/css/product-creation-page.css">
    <link rel="stylesheet" href="style/css/admin.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>
<body>
    <?php 
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>

<main>
    <?php 
    if ($found) {
        $product = $page->getProduct();
        $overviews = $product->getOverviews();
        echo "<title>Editing product - ".$product->getName()."</title>";

        // Product exists create page markup
        ?>
        <div class='admin-page-header'>
            <div class="admin-header-text-container">
                <h1>Getwhisky Editing - <?php echo htmlentities($product->getName(), ENT_QUOTES); ?></h1>
                <p><a href="/admin-product-management.php">Back to Product Management</a></p>
            </div>
        </div>

        <div id='product-root'>
            <input type="hidden" id="product-id" value="<?php echo $product->getId(); ?>">
            <div class="product-top-container">

                <div class="product-top-left">
                    <div class="product-creation-input-container">
                        <label for="product-image">Change Product Image</label>
                        <div class="input-container-100 input-btn-container" >
                            <input type="file" id="product-image" id="" class="form-item" autocomplete="off"/>
                            <button id='update-product-image' class='update-button'>Update</button>
                        </div>
                    </div>
                    <img src="<?php echo $product->getImage();?>" alt="" id='product-image-preview'>
                </div>

                <div class="product-top-right">
                    <!-- Product Name -->
                    <div class="product-creation-input-container">
                        <label for="product-name">Product Name</label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="text" id="product-name" id="" class="form-item" autocomplete="off" value="<?php echo htmlentities($product->getName(), ENT_QUOTES); ?>"/>
                            <button id='update-product-name' class='update-button'>Update</button>
                        </div>
                        
                    </div>

                    <!-- Product Type -->
                    <div class="product-creation-input-container">
                        <label for="product-type">Product Type</label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="text" id="product-type" id="" class="form-item" autocomplete="off" value="<?php echo htmlentities($product->getType(), ENT_QUOTES); ?>">
                            <button id="update-product-type" class='update-button'>Update</button>
                        </div>
                    </div>

                    <!-- Product Price & Stock -->
                    <div class="product-creation-input-container">
                        <label for="product-price">Price</label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="number" step="0.01" id="product-price" id="" class="form-item" autocomplete="off" value="<?php echo $product->getPrice(); ?>">
                            <button id="update-product-price" class='update-button'>Update</button>
                        </div>
                        <label for="product-Stock">Initial Stock</label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="number" id="product-stock" id="" class="form-item" autocomplete="off" value="<?php echo $product->getStock(); ?>">
                            <button id="update-product-stock" class='update-button'>Update</button>
                        </div>
                    </div>

                    <!-- Product Desc -->
                    <div class="product-creation-input-container">
                        <label for="product-desc">Product Description</label>
                        <div class="input-container-100 input-btn-container">
                            <textarea id="product-desc" id="" class="form-item"><?php echo htmlentities($product->getDescription(), ENT_QUOTES); ?></textarea>
                            <button id="update-product-desc" class='update-button'>Update</button>
                        </div>
                    </div>

                    <!-- Optional values -->
                    <div class="product-creation-input-container bg-white" style='margin-top:40px;border:1px solid lightgrey;padding:20px;border-radius:3px;'>
                        <div class="section-header">
                        <h3>Additional Details<span style='font-style:italic;font-size:1.4rem;font-weight:400;opacity:0.8;'> (Fill if applicable)</span></h3>

                        </div>
                        <label for="product-alc-volume">Alchohol Volume <i style='cursor:default;font-size:1.4rem;'>(Example: 40%)</i></label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="text" id="product-alc-volume" id="" class="form-item" autocomplete="off" value="<?php echo htmlentities($product->getAlcoholVolume(), ENT_QUOTES); ?>">
                            <button id="update-product-alc-volume" class='update-button'>Update</button>
                        </div>
                        <label for="product-bottle-size">Bottle Size <i style='cursor:default;font-size:1.4rem;'>(Example: 70cl / 2 x 20cl)</i></label>
                        <div class='input-container-100 input-btn-container'>
                            <input type="text" id="product-bottle-size" id="" class="form-item" autocomplete="off" value="<?php echo htmlentities($product->getBottleSize(), ENT_QUOTES); ?>">
                            <button id="update-product-bottle-size" class='update-button'>Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-bottom-container">
                <div class="product-bottom-left"></div>

                <div class="product-bottom-right">
                    
                    <!-- Product category -->
                    <div class='product-category-options-container'>
                        <div class='section-header'>
                            <h3>Category Specific Details</h3>
                        </div>
                        <input type="hidden" id='current-product-category' value="<?php echo $product->getCategoryId(); ?>">
                        <?php 
                            
                            $categoryList = new ProductCategoryList();
                            echo $categoryList;
                        ?>
                        <div id="product-attribute-selection">
                            <!-- Content added here via JS -->
                        </div>
                        <button id='update-product-attributes'>Update Product Attributes</button>
                    </div>

                    <div class="product-overview-creation-container">
                        <div class='section-header'>
                            <h3>Product Overviews <span style='font-style:italic;font-size:1.4rem;font-weight:400;opacity:0.8;'> Extra Details & Info</span></h3>
                        </div>
                        <?php 
                        foreach($overviews as $overview) {
                            echo "<div class='product-overview-item' id='product-overview-item-".$overview['id']."'>";
                                // Title
                                echo "<div class='input-container-100'>";
                                    echo "<label>Title</label><input type='text' class='form-item' id='product-overview-title-".$overview['id']."' value='".htmlentities($overview['heading'], ENT_QUOTES)."'>";
                                echo "</div>";
                                // Image
                                echo "<div class='input-container-100'>";
                                    echo "<label>Image <span class='optional'>-optional</span></label><input type='file' class='form-item' id='product-overview-image-".$overview['id']."'>";
                                    echo "<img src='".$overview['image']."' style='margin-top:20px;max-width:100%;max-height:300px;' id='current-product-overview-image-".$overview['id']."'>";
                                echo "</div>";
                                // Text
                                echo "<div class='input-container-100'>";
                                    echo "<label>Text content</label><textarea class='form-item' id='product-overview-text-".$overview['id']."'>".htmlentities($overview['text_body'], ENT_QUOTES)."</textarea>";
                                echo "</div>";

                                echo "<button name='remove-overview-section' overview-id='".$overview['id']."'>Remove</button>";
                                echo "<button name='update-overview-section' overview-id='".$overview['id']."'>Update</button>";

                            echo "</div>";
                        }
                        ?>
                        <input type="hidden" id='count-overview' value='0'>
                        <button id='create-overview'>Add New Overview</button>
                    </div>

                    <button id='save-product'>Upload Product</button>
                </div>

            </div>
            
        </div>
        <?php
    }
    ?>

</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/edit-product-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductEditPage();
        }
    }
</script>
</html>