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

        <div class="checkout-sign-up-banner">
            <div class="content-container">
                <p><a href="/products.php?catid=<?php echo $_SESSION['last_viewed_category']?>" class="continue-shopping">Continue shopping</a></p>
                <div class='cart-contents-container'>
                    <p>Your cart contains <?php echo count($page->getCart()->getItems())?> item(s) (£<?php echo $page->getCart()->getCartTotal();?>)</p>
                    <p><a href="/cart.php" class="cart-return-btn">Edit</a></p>
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