<!doctype html>
<head>
	<meta charset="UTF-8" />
</head>
<body>
<?php
	require_once("php/page.class.php");
	try {
		if(util::valEmail($_POST['email']) && util::valStr($_POST['userpass'])) {
			$checkoutLogin = false;
			if (isset($_POST['checkout'])) $checkoutLogin = true;
			$email=$_POST['email'];
			$userpass=$_POST['userpass'];
			$page=new Page();
			$page->login($email,$userpass, $checkoutLogin);
		}
	} catch(Exception $e) {
		echo "Error : ", $e->getMessage();
	}
?>
</body>
</html>
