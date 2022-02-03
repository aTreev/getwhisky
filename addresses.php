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
    <link rel="stylesheet" href="style/css/user.css">
	<link rel="stylesheet" href="style/css/address-page.css" />
	<title>user page</title>
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
	
    <div id="add-address-modal">
            <form class='form-main'>
                <div class="form-header">
                    <h3>New delivery address</h3>
                </div>
                <div class="input-container-100">
                    <label>Address Name*</label>
                    <input class="form-item" type="text" id="add-identifier" name="add-address" autocomplete="no" maxlength=50 /><span></span>
                </div>
                <div class="input-container-100">
                    <label>Recipient full name*</label>
                    <input class="form-item" type="name" id="add-full-name" name="add-address" maxlength=90 /><span></span>
                </div>
                <div class="input-container-100">
                    <label>Phone number</label>
                    <input class="form-item" type="tel" id="add-mobile" name="add-address" maxlength=12 /><span></span>
                </div>
                <div class="input-container-100">
                    <label>Postcode*</label>
                    <input class="form-item" type="postcode" id="add-postcode" name="add-address" maxlength=10 /><span></span>
                </div>
                <div class="input-container-100">
                    <label>Address Line 1*</label>
                    <input class="form-item" type="street" id="add-line1" name="add-address" maxlength=80 /><span></span>
                </div>
                <div class="input-container-100">
                    <label>Address Line 2</label>
                    <input class="form-item" type="street" id="add-line2" name="add-address" maxlength=80 /><span></span>
                </div>
                <div class="input-container-50">
                    <div>
                        <label>Town/City*</label>
                        <input class="form-item-50" type="city" id="add-city" name="add-address" maxlength=50 /><span></span>
                    </div>
                    <div>
                        <label>County</label>
                        <input class="form-item-50" type="county" id="add-county" name="add-address" maxlength=50 /><span></span>
                    </div>
                </div>
                <div class="input-container-100">
                    <button type="submit" id="new-address-submit">Submit</button>
                </div>
            </form>
        </div>
	<main>
        <div class='account-header'>
            <h2>Your delivery addresses</h2>
        </div>
        <div id="user-root">
            <?php echo $page->getUser()->displayAccountOptionsSidebar()?>
            <div class="account-main-content">
                <div class="form-header">
                    <h3>Manage your delivery addresses</h3>
                </div>
                <div id="address-root" style='max-width: 100%;margin-top:0;margin-bottom:0;padding:0;'>
                    <?php echo $page->getUser()->getAndDisplayAddressPage(); ?>
                </div>
            </div>
        </div>
        
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/user-address-functions.js"></script>
<script>
	document.onreadystatechange = function(){
        if(document.readyState=="complete") {
			prepareMenu();
            prepareUserAddressPage();
        }
    }
</script>
</html>