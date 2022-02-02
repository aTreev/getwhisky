<?php 
    require_once("php/page.class.php");
    $page = new Page();
    if (count($page->getCart()->getItems()) == 0) header("Location: /cart.php");
    if ($page->getUser()->getUsertype() == 0) header("Location: /checkout-sign-up.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <link rel="stylesheet" href="style/css/address-page.css">
    <link rel="stylesheet" href="style/css/cart.css">

    <title>getwhisky delivery options</title>
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
        <div class="cart-position-container">
            <div class="content-container">
                <div class="position-item">
                    <a href='/cart.php' class='previous-link'>
                        <i class="position-number fas fa-check"></i>
                        <p class="position-text">Basket</p>
                    </a>
                </div>
                <div class="position-item">
                <a href='#' class='previous-link'>
                        <i class="position-number fas fa-check"></i>
                        <p class="position-text">Details</p>
                    </a>
                </div>
                <div class="position-item position-active">
                    <p class="position-number">3</p>
                    <p class="position-text">Delivery</p>
                </div>
                <div class="position-item">
                    <p class="position-number">4</p>
                    <p class="position-text">Payment</p>
                </div>
                <div class="position-item">
                    <p class="position-number">5</p>
                    <p class="position-text">Thanks!</p>
                </div>
            </div>
        </div>

        <div class="cart-details-banner">
            <div class="content-container">
                <?php 
                    $continueShoppingLink = "/index.php";
                    if (isset($_SESSION['last_viewed_category'])) {
                        $continueShoppingLink = "/products.php?catid=".util::sanInt($_SESSION['last_viewed_category']);
                    }
                ?>
                <p><a href="<?php echo $continueShoppingLink ?>" class='continue-shopping'>Continue shopping</a></p>
                <div class='cart-contents-container'>
                    <p>Your cart contains <?php echo $page->getCart()->getCartItemCount(); ?> item(s) (£<?php echo $page->getCart()->getCartTotal();?>)</p>
                    <a href="/cart.php" class="cart-return-btn">Edit</a>
                </div>
            </div>
        </div>

        <div id="address-root">
            <h3>Please select the address to deliver to</h3>
            <?php echo $page->getUser()->getAndDisplayAddressPage(); ?>
        </div>
        

        <!-- 
        <div class="delivery-selection">
            <form class='form-inline' method="POST" action="/php/create-checkout-session.php">
                <h3>Delivery Method</h3>
                <p style='margin-bottom:10px;'>Please select your preferred delivery option</p>
                <input type='hidden' name='addressId' value='' />
                <select name="deliveryType" class='form-item'>
                    <option value=1 style='font-size:1.6rem;'>£4.49 - Standard delivery</option>
                    <option value=2 style='font-size:1.6rem;'>£5.99 - first class</option>
                </select>
                <button type="submit" id='delivery-submit'>Proceed to payment</button>
            </form>
        </div>
        -->
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