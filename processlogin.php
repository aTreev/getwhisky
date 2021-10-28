<!doctype html>
<head>
	<meta charset="UTF-8" />
</head>
<body>
<?php
	require_once("php/page.class.php");
	try {
		$username=$_POST['username'];
		$userpass=$_POST['userpass'];
		$page=new Page();
		$page->login($username,$userpass);
	} catch(Exception $e) {
		echo "Error : ", $e->getMessage();
	}
?>
</body>
</html>
