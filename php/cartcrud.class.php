<?php 
require_once("db.php");

class CartCRUD {
    private static $db;
    private $sql, $stmt;

    public function __construct() {
        self::$db = db::getInstance();
    }

    /************
     * returns a user's cart that hasn't been checked out
     *********/
    public function getUserCart($userid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM cart WHERE userid = ? AND checked_out = 0;";
		$this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    /**************
     * Retrieves existing cart ids for unique id generation
     ***********/
    public function getExistingCartIds($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT id FROM cart;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function createNewCart($cartId, $userId) {
        $today = date("Y-d-m");
        $this->sql = "INSERT INTO cart (`id`, `userid`, `created_date`, `checked_out`) VALUES (?,?,?,0)";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("sss", $cartId, $userId, $today);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function getCartItems($cartId, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM cart_items WHERE cart_id = ?;";
		$this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("s", $cartId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function getCartItemDetailsByProductId($productId, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM products WHERE id = ?";
		$this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("i",$productId);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function updateCartItemQuantity($cartId, $productId, $quantity) {
        $this->sql = "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("isi", $quantity, $cartId, $productId);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function removeFromCart($cartId, $productId) {
        $this->sql = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $cartId, $productId);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }

    public function addToCart($cartId, $productId) {
        $this->sql = "INSERT INTO cart_items (`cart_id`, `product_id`, `quantity`) VALUES (?,?,1);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("si", $cartId, $productId);
        $this->stmt->execute();
        if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
    }
}
?>