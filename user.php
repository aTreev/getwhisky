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
			<h2>My account</h2>
		</div>
		<div id="user-root" style='margin-top:20px;'>
			<?php 
				echo $page->getUser()->displayAccountOptionsSidebar();
				echo $page->getUser(); 
			?>
		</div>
		
	</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/user-page-functions.js"></script>
<script>
	document.onreadystatechange = function(){
        if(document.readyState=="complete") {
            prepareUserPage();
			prepareMenu();
        }
    }
</script>
</html>
