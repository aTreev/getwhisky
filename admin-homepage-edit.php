<?php
require_once("php/page.class.php");
$page = new Page(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>Homepage admin panel</title>
    <link rel="stylesheet" href="style/css/admin.css">
</head>
<body>
    <?php 
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
<main>
    <?php echo $page->getUser()->admin()->displayHomepageEditPage(); ?>
</main>

<div id="create-banner-modal">
    <form class='form-inline'>
        <div class="form-header">
            <h3>New homepage banner</h3>
        </div>
        <div class="input-container-100">
            <input class="form-item" type="text" id="create-banner-heading" name="add-address" placeholder="Banner heading" autocomplete="no" maxlength=50 /><span></span>
        </div>
        <div class="input-container-100">
            <textarea class="form-item" id="create-banner-smalltext" resize="none" placeholder="Banner sub-heading"></textarea>
        </div>
        <div class="input-container-100">
            <input class="form-item" type="file" id="create-banner-image-upload" />
            <img id='create-banner-image-preview' style='max-height:150px;' src="" />
        </div>
        <div class="input-container-100">
        <input type="color" id="create-banner-background-colour">
        </div>
        <div class="input-container-100">
            <button type='submit' id='create-banner-preview' style='background-color:var(--bg-primary);'>Preview Banner</button>
            <button type='submit' id='create-banner-submit'>Submit</button>
        </div>
    </form>
</div>      
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/admin-functions.js"></script>

<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareHomepageEditPage()
        }
    }
</script>
</html>