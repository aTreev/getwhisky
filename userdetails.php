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
	<title>user page</title>
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
	
	<main>
        <div class="account-header">
            <h2>My Personal Details</h2>
            <a href="/user.php">back to user profile</a>
        </div>
        <div id="user-root">
            <?php echo $page->getUser()->displayAccountOptionsSidebar();?>
            <div class="account-main-content">
                <div id="user-detail-root">
                    <form action="" class='form-main' style='margin-top:0;width:100%;max-width:1000px;box-shadow:none;padding-top:0;'id='user-details-form'>
                        <div class="form-header">
                            <h3>Update your personal details</h3>
                        </div>
                        <div class="input-container-100">
                            <label for="first-name">First name</label>
                            <input type="text" id='first-name' class='form-item-slim' value='<?php echo htmlentities(ucwords($page->getUser()->getFirstName())); ?>'>
                        </div>

                        <div class="input-container-100">
                            <label for="surname">Surname</label>
                            <input type="text" id='surname' class='form-item-slim' value='<?php echo htmlentities(ucwords($page->getUser()->getSurname())); ?>'>
                            
                        </div>

                        <div class="input-container-100">
                            <label for="email">Email address</label>
                            <input type="email" id="email" class='form-item-slim' value='<?php echo htmlentities($page->getUser()->getEmail()); ?>'>
                        </div>

                        <div class="background-grey-p-20">
                            <div class="info-area-red">
                                <p>Leave blank if you do not wish to change your password</p>
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="input-container-100">
                                <label for="password">New Password</label>
                                <input type="password" class='form-item-slim' id='password'>
                            </div>
                            <div class="input-container-100">
                                <label for="repeat-password">Repeat password</label>
                                <input type="password" class='form-item-slim' id="repeat-password">
                                <p class="form-info">Create a secure password by combining numbers, uppercase, lowercase and symbols.</p>
                            </div>
                        </div>

                        <div class="input-container-100" style='margin-top:20px;'>
                            <button type='submit'>Save Changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="modal-container ignore-overlay" id="password-confirm-modal">
            <div class='modal-header'>
                <p>Confirm your password</p>
                <i class='fas fa-times' id='close-password-confirm-modal'></i>
            </div>
            <div class='modal-content' id='reset-password-form'>
                <p>Please enter your getwhisky password to save changes made to your account</p>
                <form id="password-confirm-form">
                    <div class="input-container-100">
                        <label for="">Password</label>
                        <input class='form-item' type="password" id='password-confirm-input' placeholder=""/>
                        <p id='invalid-password-message' style='color:darkred;'></p>
                    </div>
                    <button type="submit">submit</button>
                </form>
            </div>
        </div>
	</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/user-detail-page.js"></script>
<script>
	document.onreadystatechange = function(){
        if(document.readyState=="complete") {
            prepareMenu();
            prepareUserDetailsPage();
        }
    }
</script>
</html>
