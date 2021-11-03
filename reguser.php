<?php
	require_once("php/user.class.php");
	require_once("php/unique-id-generator.class.php");
	// posts all the data to the registerUser function in the user.class.php
	// presents a login form on an ok registration
	// otherwise posts the error messages with a link to go back to the register.html

	//includes to generate an anonymous menu
	require_once("php/page.class.php");
	$page = new Page(0);
?>
<!doctype html>
<html lang="en">
<head>
	<?php echo $page->displayHead(); ?>
</head>
<body>
<?php

	try {
		$uniqueIdGenerator = new UniqueIdGenerator("userid");
		$userid = $uniqueIdGenerator->getUniqueId();
		$uniqueIdGenerator = new UniqueIdGenerator("vkey");
		$vKey = $uniqueIdGenerator->getUniqueId();
		$username=$_POST['username'];
		$firstname=$_POST['firstname'];
		$surname=$_POST['surname'];
		$email=$_POST['email'];
		$dob=$_POST['dob'];
		$userpass=$_POST['userpass'];

		$result = $page->registerUser($userid,$username,$userpass,$firstname,$surname,$email,$dob, $vKey)
?>
			<!-- menu display -->
			<header>
				<?php echo $page->displayHeader(); ?>
			</header>
			<main>
			<?php 
			if ($result['insert']) {
				?>
				<h3>Thank you for registering with getwhisky</h3>
				<p>A verification email has been sent to <?php echo $email; ?></p>
				<p>Please follow the link in this email to verify your account</p> 
				<a href='/user.php'>Your account</a>
				<?php
			} else {
				foreach ($result['messages'] as $message) {
					echo "<p>".$message."</p>";
				}
			?><a href="javascript:history.back();">Back to Registration Form</a><?php
		}
	} catch (Exception $e) {
		echo "Error : ", $e->getMessage();
	}	
?>
</main>
</body>
</html>
