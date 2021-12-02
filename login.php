<?php
    require_once("php/page.class.php");
    $page = new Page(0);
?>
<!doctype html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky login</title>
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <main>
        <form method="post" action="processlogin.php" class="form-main">
            <div class="form-header">
                <h3>getwhisky sign in</h3>
            </div>
            <div class="input-container">
            <input class="form-item" type="text" name="email" id="email" placeholder="email"/>
            </div>
            <div class="input-container">
            <input class="form-item" type="password" name="userpass" id="userpass" placeholder="password"/>

            </div>
            <p class='form-link' id='forgot-password'><a href='#'>Forgotten password?</a></p>
            <p class='form-link'><a href='/register.php'>New to getwhisky? sign up here</a></p>

            <div class="submit">
                <button type="submit">Login</button>
            </div>
        </form>


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
<script src="js/login-functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareLoginPage();
            prepareMenu();
        }
    }
</script>
</html>
