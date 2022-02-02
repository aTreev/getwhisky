<?php
    require_once("php/page.class.php");
    $page = new Page(0);
    if ($page->getUser()->getUsertype() > 0) { header("Location: /user.php"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <link rel='stylesheet' href='style/css/register.css'>
    <title>getwhisky registration</title>
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <?php 
                //Guest session tests
                //echo "Session userid: ".$_SESSION['userid'];
                //echo "<br>";
                //echo "page userid: ".$page->getUser()->getUserid();
            ?>
    <main>
        <form method="post" action="reguser.php" class="form-main" id="regform">
            <div class="form-header">
                <h3>
                    getwhisky sign up
                </h3>
            </div>

            <div class="input-container-100">
                <label for="email">Email</label>
                <input class="form-item" type="email" id="email" name="email" required />
            </div>
            
            <div class="input-container-50">
                <div>
                    <label for="firstname">First name</label>
                    <input class="form-item-50" type="text" id="firstname" name="firstname" required />
                </div>
                <div>
                    <label for="surname">Surname</label>
                    <input class="form-item-50" type="text" id="surname" name="surname" required />  
                </div>
                

            </div>

            <div class="input-container-100">
                <label for="userpass">Password</label>
                <input class="form-item" type="password" id="userpass" name="userpass" required />
            </div>

            <div class="input-container-100">
                <label for="repeatUserpass">Repeat Password</label>
                <input class="form-item" type="password" id="repeatUserpass" name="repeatPassword" required />
            </div>

            <div class="submit">
                <button type="submit" id="submitbutton" name="submitbutton">Create Account</button>
            </div>
        </form>
    </main>
</body>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/registration.js"></script>
<script>
    'use strict';
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareRegistrationForm();
            prepareMenu();
        }
    }
</script>
</html>
