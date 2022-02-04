<?php
require_once("php/page.class.php");
$page = new Page(0);
switch($page->getUser()->getUserType()) {
    case 1: header("Location: suspended.php"); break;
    case 2: header("Location: user.php"); break;
    case 3: header("Location: admin.php"); break;
}
    
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
        <?php 
            if (isset($_SESSION['getwhisky_inactivity_logout'])) {
                ?>
                <?php
                echo "<div class='inactivity-logout-container'>";
                    echo "<div class='inactivity-logout-content'>";
                        echo "<p>".htmlentities($_SESSION['getwhisky_inactivity_logout'])."</p>";
                        echo "<i class='fas fa-info-circle'></i>";
                    echo "</div>";
                echo "</div>";
                unset($_SESSION['getwhisky_inactivity_logout']);
            }
        ?>
        <form method="post" action="processlogin.php" class="form-main" id='login-form'>
            <div class="form-header">
                <h3>getwhisky sign in</h3>
            </div>
            <div class="input-container-100">
            <label for="email">Email</label>
            <input class="form-item" type="text" name="email" id="login-email" />
            </div>
            <div class="input-container-100">
            <label for="userpass">Password</label>
            <input class="form-item" type="password" name="userpass" id="login-password" />

            </div>
            <p class='form-link' id='forgot-password'><a href='#'>Forgotten password?</a></p>
            <p class='form-link'><a href='/register.php'>New customer? sign up here</a></p>

            <div class="submit">
                <button type="submit" id='login-submit'>Login</button>
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
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareLoginPage();
            prepareMenu();
        }
    }
</script>
</html>
