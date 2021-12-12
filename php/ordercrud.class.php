<?php
require_once("db.php");

class OrderCRUD {
    private static $db;
    private $sql, $stmt;

    public function __construct() {
        self::$db = db::getInstance();
    }

    public function getExistingOrderIds($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT order_id FROM orders;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function createOrder($orderid, $userid, $addressid, $deliveryTypeId, $stripePaymentIntent, $dateTimeAdded, $total) {
        $this->sql = "INSERT INTO orders (`order_id`, `userid`, `address_id`, `status_id`, `admin_status_id`, `delivery_type_id`, `stripe_payment_intent`, `date_placed`, `total`) VALUES (?,?,?,1,1,?,?,?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("sssissd", $orderid, $userid, $addressid, $deliveryTypeId, $stripePaymentIntent, $dateTimeAdded, $total);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function addToOrder($orderid, $productid, $quantity, $priceBought) {
        $this->sql = "INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price_bought`) VALUES (?,?,?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("siid", $orderid, $productid, $quantity, $priceBought);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function getUserOrders($userid, $style=MYSQLI_ASSOC) {
        $this->sql="SELECT orders.*, order_status.name AS 'status_label',  order_status_admin.name AS 'admin_status_label', delivery_type.name AS 'delivery_label', delivery_type.price AS 'delivery_cost'
                    FROM orders
                    JOIN order_status
                    ON orders.status_id = order_status.id
                    
                    JOIN order_status_admin
                    ON orders.admin_status_id = order_status_admin.id
                    
                    JOIN delivery_type
                    ON orders.delivery_type_id =  delivery_type.id
                    WHERE orders.userid = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $userid);
        $this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }
}

?>