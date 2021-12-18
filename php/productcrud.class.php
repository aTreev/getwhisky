<?php 
require_once("db.php");
class ProductCRUD {
    private static $db;
    private $sql, $stmt;

    public function __construct() {
        self::$db = db::getInstance();
    }

    public function getProducts($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM products order by id DESC";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getProductById($productId, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM products WHERE id = ?";
		$this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productId);
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

    public function getProductAttributesFull($productId, $style=MYSQLI_ASSOC) {
        $this->sql = "  SELECT attribute.title, attribute_value.value  FROM attribute_value
                        JOIN entity_value
                        ON entity_value.attribute_value_id = attribute_value.id
                        JOIN attribute
                        ON attribute_value.attribute_id = attribute.id
                        WHERE entity_value.product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productId);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }   

    public function getProductOverviews($productId, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT image, heading, text_body FROM product_overviews WHERE product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i", $productId);
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

    public function updateProductStock($productid, $quantity) {
        $this->sql = "UPDATE `products` SET `stock` = (stock - ?) WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ii", $quantity, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }


    public function toggleProductActiveState($productid) {
        $this->sql = "UPDATE `products` SET `active` = !active WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i", $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function toggleProductFeaturedState($productid) {
        $this->sql = "UPDATE `products` SET `featured` = !featured WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i", $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
}
?>