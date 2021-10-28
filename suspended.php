<?php
	require_once("php/page.class.php");
	$page = new Page(1);
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
	<header>
		<?php
			echo $page->displayHeader();
		?>
	</header>
	<h1>User Suspended</h1>
	<!-- User data is retreieved from the __to_string method in the user.class.php -->
	<?php echo $page->getUser(); ?>
	
</body>
</html>
