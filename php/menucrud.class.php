<?php
require_once("db.php");
class MenuCRUD {
	private static $db;
	private $sql;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	
	public function getProductMenu($style=MYSQLI_ASSOC) {
		$sql = "SELECT `id`, `name`, `description` FROM categories;";
		$stmt = self::$db->prepare($sql);
		$stmt->execute();
		$result = $stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getProductFiltersByCategoryId($categoryId, $style=MYSQLI_ASSOC) {
		$sql = "SELECT * FROM attribute WHERE category_id = ?;";
		$stmt = self::$db->prepare($sql);
		$stmt->bind_param("i", $categoryId);
		$stmt->execute();
		$result = $stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getProductFilterValuesByCategoryId($categoryId, $style=MYSQLI_ASSOC) {
		$sql = "SELECT attribute_value.*, (SELECT COUNT(*) FROM entity_value WHERE entity_value.attribute_value_id = attribute_value.id) AS 'count_products_with_value' FROM attribute_value
				JOIN attribute
				ON attribute.id = attribute_value.attribute_id
				WHERE attribute.category_id = ?
				ORDER BY attribute_value.value;
				";
		$stmt = self::$db->prepare($sql);
		$stmt->bind_param("i", $categoryId);
		$stmt->execute();
		$result = $stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
}
?>
