<?php 
require_once("db.php");
class ProductCRUD {
    private static $db;
    private $sql;

    public function __construct() {
        self::$db = db::getInstance();
    }

    public function getProducts($style=MYSQLI_ASSOC) {
        $sql = "SELECT * FROM products";
		$stmt = self::$db->prepare($sql);
		$stmt->execute();
		$result = $stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getProductAttributeValueIds($productId, $style=MYSQLI_ASSOC) {
        $sql = "SELECT attribute_value_id FROM entity_value WHERE product_id = ?;";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("i",$productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }
    
}
?>