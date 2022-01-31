<?php
    require_once("php/page.class.php");
    $page = new Page(0);
    if (count($page->getCart()->getItems()) == 0) header("Location: /cart.php");
    if ($page->getUser()->getUserType() != 0) header("Location: /checkout.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky Checkout</title>
    <link rel="stylesheet" href="style/css/cart.css">
    <link rel='stylesheet' href='style/css/register.css'>
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>

    <main>
        <div class="cart-position-container">
            <div class="content-container">
                <div class="position-item">
                    <a href='/cart.php' class='previous-link'>
                        <i class="position-number fas fa-check"></i>
                        <p class="position-text">Basket</p>
                    </a>
                </div>
                <div class="position-item position-active">
                    <p class="position-number">2</p>
                    <p class="position-text">Details</p>
                </div>
                <div class="position-item">
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

        <div class='cart-details-banner'>
            <div class='content-container'>
                <?php 
                    $continueShoppingLink = "/index.php";
                    if (isset($_SESSION['last_viewed_category'])) {
                        $continueShoppingLink = "/products.php?catid=".util::sanInt($_SESSION['last_viewed_category']);
                    }
                ?>
                <p><a href="<?php echo $continueShoppingLink ?>" class='continue-shopping'>Continue shopping</a></p>
                <div class='cart-contents-container'>
                    <p>Your cart contains <?php echo $page->getCart()->getCartItemCount(); ?> item(s) (£<?php echo $page->getCart()->getCartTotal();?>)</p>
                    <a href='/cart.php' class='cart-return-btn'>Edit</a>
                </div>
            </div>
        </div>

        <div class="checkout-sign-up-container">
            <div class="content-container">
                <div class="left">
                    <div class="checkout-sign-up-header">
                        <p>New Customer</p>
                    </div>
                    <form action="" class="form-inline">
                        <div class="input-container-100">
                            <label for="email">Email Address</label>
                            <input class="form-item" type="email" id="email" name="email" required />
                        </div>
                        
                        <div class="input-container-50">
                            <div>
                                <label for="firstname">First Name</label>
                                <input class="form-item-50" type="text" id="firstname" name="firstname" required />
                            </div>
                            <div>
                                <label for="surname">Last Name</label>
                                <input class="form-item-50" type="text" id="surname" name="surname" required />
                            </div>

                        </div>

                        <div class="input-container-100">
                            <label for="userpass">Password</label>
                            <input class="form-item" type="password" id="userpass" name="userpass" required />
                        </div>

                        <div class="input-container-100">
                            <label for="repeatPassword">Repeat Password</label>
                            <input class="form-item" type="password" id="repeatUserpass" name="repeatPassword" required />
                        </div>

                        <div class="submit">
                            <button type="submit" id="submitbutton" name="submitbutton">Create Account</button>
                        </div>
                    </form>
                </div>
                <div class="right">
                    <div class="checkout-sign-up-header">
                        <p>Returning Customer</p>
                    </div>

                    <!-- Login -->
                    <form action="processlogin.php" method="POST" class="form-inline">
                        <input type="hidden" name="checkout" value="1" />
                        <div class="input-container-100">
                            <label for="email">Email Address</label>
                            <input class="form-item" type="text" name="email" id="login-email"/>
                        </div>
                        <div class="input-container-100">
                            <label for="userpass">Password</label>
                            <input class="form-item" type="password" name="userpass" id="login-userpass"/>
                        </div>
                        <p class='form-link' id='forgot-password'><a href='#'>Forgotten password?</a></p>
                        <div class="submit">
                            <button type="submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal-container ignore-overlay" id="password-reset-modal">
            <div class='modal-header'>
                <p>Forgotten Password</p>
                <i class='fas fa-times' id='close-password-reset-modal'></i>
            </div>
            <div class='modal-content' id='reset-password-form'>
                <p>If you have forgotten your password, please enter the email address you use to log in to getwhisky. We will then email you a link to reset the password to your registered account.</p>
                <form>
                <p id='invalid-email-message' style='color:darkred;'></p>
                <div class="input-container-100">
                    <i class="input-icon far fa-envelope" aria-hidden="true"></i>
                    <input class='form-item' type="email" id='password-reset-email-input' placeholder="email"/>
                </div>
                <button type="submit" id="submit-password-reset">submit</button>
                </form>
            </div>
        </div>
    </main> 

</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/login-functions.js"></script>
<script src="js/registration.js"></script>

<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareLoginPage();
            prepareRegistrationForm();
        }
    }
</script>
</html>