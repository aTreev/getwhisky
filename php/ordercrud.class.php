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

    public function getOrderById($orderid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM orders WHERE order_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $orderid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function createOrder($orderid, $userid, $addressid, $deliveryLabel, $deliveryCost, $stripePaymentIntent, $dateTimeAdded, $total) {
        $this->sql = "INSERT INTO orders (`order_id`, `userid`, `address_id`, `status_id`, `admin_status_id`, `delivery_label`, `delivery_paid`, `stripe_payment_intent`, `date_placed`, `total`) VALUES (?,?,?,1,1,?,?,?,?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ssssdssd", $orderid, $userid, $addressid, $deliveryLabel, $deliveryCost, $stripePaymentIntent, $dateTimeAdded, $total);
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
        $this->sql="SELECT orders.*, order_status.name AS 'status_label',  order_status_admin.name AS 'admin_status_label'
                    FROM orders
                    JOIN order_status
                    ON orders.status_id = order_status.id
                    JOIN order_status_admin
                    ON orders.admin_status_id = order_status_admin.id
                    WHERE orders.userid = ?
                    ORDER BY date_placed DESC;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $userid);
        $this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getOrderFullByOrderid($orderid, $style=MYSQLI_ASSOC) {
        $this->sql="SELECT orders.*, order_status.name AS 'status_label',  order_status_admin.name AS 'admin_status_label'
                    FROM orders
                    JOIN order_status
                    ON orders.status_id = order_status.id
                    JOIN order_status_admin
                    ON orders.admin_status_id = order_status_admin.id
                    WHERE orders.order_id = ?
                    ORDER BY date_placed DESC;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $orderid);
        $this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getOrderItems($orderid, $style = MYSQLI_ASSOC) {
        $this->sql="SELECT order_items.*, products.name, products.image
                    FROM order_items
                    JOIN products
                    ON order_items.product_id = products.id
                    WHERE order_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $orderid);
        $this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getAllOrders($style = MYSQLI_ASSOC) {
        $this->sql = "  SELECT orders.*, order_status.name AS 'status_label',  order_status_admin.name AS 'admin_status_label'
                        FROM orders
                        JOIN order_status
                        ON orders.status_id = order_status.id
                        JOIN order_status_admin
                        ON orders.admin_status_id = order_status_admin.id
                        ORDER BY date_placed DESC";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function updateOrderStatus($orderid, $status) {
        switch($status) {
            case "processing":
                $orderStatus = 1;
            break;
            case "dispatched":
                $orderStatus = 2;
            break;
            case "refunded":
                $orderStatus = 3;
            break;
            case "partial_refund":
                $orderStatus = 4;
            break;
        }

        $this->sql = "UPDATE orders SET status_id = ? WHERE order_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("is", $orderStatus, $orderid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateAdminOrderStatus($orderid, $status) {
        switch($status) {
            case "payment_received":
                $orderStatus = 1;
            break;
            case "dispatched":
                $orderStatus = 2;
            break;
            case "refunded":
                $orderStatus = 3;
            break;
            case "partial_refund":
                $orderStatus = 4;
            break;
            case "refund_failure":
                $orderStatus = 5;
            break;
        }

        $this->sql = "UPDATE orders SET admin_status_id = ? WHERE order_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("is", $orderStatus, $orderid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function updateOrderRefundAmount($refundAmount, $orderid) {
        $this->sql = "UPDATE orders SET refund_amount = ? WHERE order_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ds", $refundAmount, $orderid);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
}

?>