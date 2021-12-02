<!doctype html>
<head>
	<meta charset="UTF-8" />
</head>
<body>
<?php
	require_once("php/page.class.php");
	try {
		$email=$_POST['email'];
		$userpass=$_POST['userpass'];
		$page=new Page();
		$page->login($email,$userpass);
	} catch(Exception $e) {
		echo "Error : ", $e->getMessage();
	}
?>
</body>
</html>
