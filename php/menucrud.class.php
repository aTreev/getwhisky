<?php
require_once("db.php");
class MenuCRUD {
	private static $db;
	private $sql;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	
	public function getMenu($menulevel, $style=MYSQLI_ASSOC) {
		$sql = "select pagename,url from MenuPage join MenuLevel on MenuPage.pageid = MenuLevel.pageid where minul<=? and maxul>=? order by menuorder,pagename;";
		$stmt = self::$db->prepare($sql);
		$stmt->bind_param("ii",$menulevel,$menulevel);
		$stmt->execute();
		$result = $stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
}
?>
