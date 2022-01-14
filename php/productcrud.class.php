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

    public function getProductById($productid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM products WHERE id = ?";
		$this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getProductAttributeValueIds($productid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT attribute_value_id FROM entity_value WHERE product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productid);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }

    public function getProductAttrValIdsAndAttrIds($productid, $style=MYSQLI_ASSOC) {
        $this->sql = "  SELECT attribute_value.attribute_id, entity_value.attribute_value_id
                        FROM attribute_value
                        JOIN entity_value
                        ON entity_value.attribute_value_id = attribute_value.id
                        WHERE entity_value.product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productid);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }

    public function getProductAttributesFull($productid, $style=MYSQLI_ASSOC) {
        $this->sql = "  SELECT attribute.title, attribute_value.value  FROM attribute_value
                        JOIN entity_value
                        ON entity_value.attribute_value_id = attribute_value.id
                        JOIN attribute
                        ON attribute_value.attribute_id = attribute.id
                        WHERE entity_value.product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productid);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }   

    public function getProductOverviews($productid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT id, image, heading, text_body FROM product_overviews WHERE product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i", $productid);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }

    public function createProductDiscount($productid, $price, $endDatetime) {
        $this->sql = "UPDATE `products` SET `discounted`= 1,`discount_price`= ?,`discount_end_datetime`= ? where id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("dsi",$price, $endDatetime, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function endProductDiscount($productid) {
        $this->sql = "UPDATE `products` SET `discounted`=0,`discount_price`=null,`discount_end_datetime`=null where id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function decreaseStockByQuantity($productid, $quantity) {
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

    public function increaseStockByQuantity($productid, $quantity) {
        $this->sql = "UPDATE `products` SET `stock` = (stock + ?) WHERE id = ?;";
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

    public function createProduct($name, $description, $image, $price, $stock, $date, $alcoholVolume, $bottleSize, $type, $categoryid) {
        $this->sql = "INSERT INTO `products`(`name`, `description`, `image`, `price`,  `stock`, `date_added`, `alcohol_volume`, `bottle_size`, `type`, `category_id`) VALUES (?,?,?,?,?,?,?,?,?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("sssdissssi", $name, $description, $image, $price, $stock, $date, $alcoholVolume, $bottleSize, $type, $categoryid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
    
    public function getLastCreatedProductId($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT id FROM products ORDER BY date_added DESC LIMIT 1";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }

    public function createProductAttribute($attributeValueId, $productid) {
        $this->sql = "INSERT INTO `entity_value` (attribute_value_id, product_id) VALUES (?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ii", $attributeValueId, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function createProductOverview($productid, $overviewImage, $overviewTitle, $overviewText) {
        $this->sql = "INSERT INTO product_overviews (`product_id`, `image`, `heading`, `text_body`) VALUES(?,?,?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("isss", $productid, $overviewImage, $overviewTitle, $overviewText);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function getLastOverviewId($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT id FROM product_overviews ORDER BY id DESC LIMIT 1;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->execute();
        $result = $this->stmt->get_result();
        $resultset=$result->fetch_all($style);
        return $resultset;
    }



    /****************************************************
     * Product Update Methods
     ***************************************************/
    public function updateProductName($name, $productid) {
        $this->sql = "UPDATE products SET name = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $name, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductType($type, $productid) {
        $this->sql = "UPDATE products SET type = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $type, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductPrice($price, $productid) {
        $this->sql = "UPDATE products SET price = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("di", $price, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductStock($stock, $productid) {
        $this->sql = "UPDATE products SET stock = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ii", $stock, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductDescription($desc, $productid) {
        $this->sql = "UPDATE products SET description = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $desc, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductBottleSize($bottleSize, $productid) {
        $this->sql = "UPDATE products SET bottle_size = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $bottleSize, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductAlcoholVolume($alcVolume, $productid) {
        $this->sql = "UPDATE products SET alcohol_volume = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $alcVolume, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function resetProductAttributes($productid) {
        $this->sql = "DELETE FROM entity_value WHERE product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i", $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductCategory($categoryid, $productid) {
        $this->sql = "UPDATE products SET category_id = ? WHERE id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ii", $categoryid, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateProductOverview($image, $title, $text, $overviewid, $productid) {
        $this->sql = "UPDATE product_overviews SET image = ?, heading = ?, text_body = ? WHERE id = ? AND product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("sssii", $image, $title, $text, $overviewid, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function deleteProductOverview($overviewid, $productid) {
        $this->sql = "DELETE FROM product_overviews WHERE id = ? AND product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ii", $overviewid, $productid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
}
?>