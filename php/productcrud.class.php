<?php 
require_once("db.php");
class ProductCRUD {
    private static $db;
    private $sql, $stmt;

    public function __construct() {
        self::$db = db::getInstance();
    }

    public function getProducts($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM products";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getProductAttributeValueIds($productId, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT attribute_value_id FROM entity_value WHERE product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productId);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }
    
    public function endProductDiscount($productId) {
        $this->sql = "UPDATE `products` SET `discounted`=0,`discount_price`=null,`discount_end_datetime`=null where id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productId);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
}
?>