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
	
	<title>user page</title>
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
	
	<main>
		<div id="user-root" style='margin-top:20px;'>
			<h1>Hello <?php echo $page->getUser()->getusername();?></h1>
			<?php 
				if ($page->getUser()->getVerifiedStatus() == 0) {
					echo "<button id='resend-validation'>Resend validation email</button>";
				}
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
