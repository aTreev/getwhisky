<?php
	require_once("php/page.class.php");
	$page = new Page(2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		echo $page->displayHead();
	?>
	<link rel="stylesheet" href="style/css/address-page.css" />
	<title>user page</title>
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
	
    <div id="add-address-modal">
            <form class='form-main'>
                <div class="form-header">
                    <h3>New delivery address</h3>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="text" id="add-identifier" name="add-address" placeholder="Name of address" autocomplete="no" maxlength=50 /><span></span>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="name" id="add-full-name" name="add-address" placeholder="Full name" maxlength=90 /><span></span>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="tel" id="add-mobile" name="add-address" placeholder="Phone number" maxlength=12 /><span></span>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="postcode" id="add-postcode" name="add-address" placeholder="postcode" maxlength=10 /><span></span>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="street" id="add-line1" name="add-address" placeholder="Address line 1" maxlength=80 /><span></span>
                </div>
                <div class="input-container-100">
                    <input class="form-item" type="street" id="add-line2" name="add-address" placeholder="Address line 2" maxlength=80 /><span></span>
                </div>
                <div class="input-container-50">
                    <input class="form-item-50" type="city" id="add-city" name="add-address" placeholder="Town/City" maxlength=50 /><span></span>
                    <input class="form-item-50" type="county" id="add-county" name="add-address" placeholder="County" maxlength=50 /><span></span>
                </div>
                <div class="input-container-100">
                    <button type="submit" id="new-address-submit">Submit</button>
                </div>
            </form>
        </div>
	<main>
        <div class='address-header'>
            <h3>Your delivery addresses</h3>
        </div>
        <div id="address-root">
            <?php echo $page->getUser()->getAndDisplayAddressPage(); ?>
        </div>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/user-address-page.js"></script>
<script>
	document.onreadystatechange = function(){
        if(document.readyState=="complete") {
			prepareMenu();
            prepareUserAddressPage();
        }
    }
</script>
</html>