<?php
require_once("db.php");
class MenuCRUD {
	/****************
	 * CRUD class for all category details and attributes
	 ****************************************************/
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	
	public function getProductCategories($style=MYSQLI_ASSOC) {
		$this->sql = "SELECT `id`, `name`, `description` FROM categories;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getCategoryDetails($categoryId, $style=MYSQLI_ASSOC) {
		$this->sql = "SELECT `name`, `description` FROM categories WHERE id = ?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$categoryId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getProductFiltersByCategoryId($categoryId, $style=MYSQLI_ASSOC) {
		$this->sql = "SELECT * FROM attribute WHERE category_id = ?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i", $categoryId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getAttributeValuesByAttributeId($attributeId, $style=MYSQLI_ASSOC) {
		$this->sql = "SELECT * FROM attribute_value WHERE attribute_id = ?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i", $attributeId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getProductFilterValuesByCategoryId($categoryId, $style=MYSQLI_ASSOC) {
		$this->sql = "SELECT attribute_value.*, (SELECT COUNT(*) FROM entity_value WHERE entity_value.attribute_value_id = attribute_value.id) AS 'count_products_with_value' FROM attribute_value
				JOIN attribute
				ON attribute.id = attribute_value.attribute_id
				WHERE attribute.category_id = ?
				ORDER BY attribute_value.value;
				";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i", $categoryId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
}
?>
