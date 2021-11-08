<?php
//The Page class, responsible for the application state, checks that the user is authorised 
require_once("user.class.php");
require_once("product-menu.class.php");
require_once("product.class.php");
require_once("product-filter.class.php");
require_once("cart.class.php");
require_once("unique-id-generator.class.php");

class Page {
	private $user;
	private $pagetype;
	private $isauthenticated;
	private $productMenu;
	private $products = [];
	private $productCategoryFilters;
	private $cart;
	
	public function __construct($pagetype=0){
		session_start();
		$this->setPagetype($pagetype);
		$this->user = new User();
		$this->productMenu = new ProductMenu();
		$this->setStatus(false);
        $this->checkUser();
		$this->getProductsFromDatabase();
		$this->initializeUserCart();
	}
	
	public function getProducts() { return $this->products; }
	public function getProductMenu() { return $this->productMenu;}
	public function getPagetype() { return $this->pagetype;}
	public function getStatus() { return $this->isauthenticated;}
	public function getUser() {return $this->user;}    
	public function getCart() { return $this->cart; }

	private function setPagetype($pagetype) {$this->pagetype=(int)$pagetype;}
	private function setStatus($status) {$this->isauthenticated=(bool)$status;}
	private function setCart($cart) { $this->cart = $cart; }
	
	// Checks for a user in the $_SESSION
	// if session is found set status is set to true and
	// the authIdSession retrieves the user details and stores them
	// in the user class
	public function checkUser() {
		// Establish guest session
		if (!isset($_SESSION['userid'])) {
			$_SESSION['userid'] = "guest-".md5(time(). bin2hex(random_bytes(10)));
			$_SESSION['guest'] = true;
		}

		if(isset($_SESSION['userid']) && $_SESSION['userid']!="") {
			
			// Guest logged in
			if (isset($_SESSION['guest']) && $_SESSION['guest'] == true) {
				$this->getUser()->setGuestUserid($_SESSION['userid']);
			} 
			
			// User logged in
			if (isset($_SESSION['guest']) && $_SESSION['guest'] == false) {
				$this->setStatus($this->getUser()->authIdSession($_SESSION['userid'],session_id()));
				$this->checkInactivityLength();
			}
		}
		
		// a catch for both guests and registered users to check if they're trying to access restricted content
		if((!$this->getStatus() && $this->getPagetype()>0) || ($this->getStatus() && $this->getUser()->getUsertype()<$this->getPagetype())) {
			$this->logout();
		}
	}
	
	private function checkInactivityLength() {
		if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
			$this->logout();
		}
		$_SESSION['last_activity'] = time();
	}
	/*******************
	 * Checks for user credentials with the authNamePass method
	 * if the hash of the passed in password matches the hash in the database
	 * the user is logged in and a session is stored in the database
	 **************************************************/
	public function login($username, $userpass) {
		session_regenerate_id();
		if($this->getUser()->authNamePass($username,$userpass)) {
			$_SESSION['guest'] = false;
			$this->getUser()->storeSession($this->getUser()->getUserid(),session_id());
			$_SESSION['userid']=$this->getUser()->getUserid();
			$_SESSION['last_activity'] = time(); // init inactivity timer

			// userlevel logic here
			switch($this->getUser()->getUsertype()) {
				case 1:
					header("location: suspended.php");
					break;
				case 2:
					header("location: user.php");
					break;
				case 3:
					header("location: admin.php");
					break;
			}
			exit();
			
		} else {
			echo "<br />Authentication failed";
		}
	}

	/****************
	 * A single use function that logs the user in on the backend
	 * during the registration process
	 **************************************************/
	public function loginDiscreet($username, $userpass) {
		session_regenerate_id();
		if($this->getUser()->authNamePass($username,$userpass)) {
			$this->getUser()->storeSession($this->getUser()->getUserid(),session_id());
			$_SESSION['userid']=$this->getUser()->getUserid();
			$_SESSION['guest'] = false;
		} else {
			echo "<br />Authentication failed";
		}
	}
	
	public function logout() {
		if(isset($_SESSION['userid']) && $_SESSION['userid']!="") {
			$this->getUser()->storeSession($_SESSION['userid']);
		}
		session_regenerate_id();
		session_unset();
		session_destroy();
		header("location: login.php");
		exit();
	}

	/*
		checks that the user is authorised to carry out the update action and if so passes the data to a 
		temporary instance of the User class.
	*/
	public function updateUser($username,$firstname,$surname,$password,$email,$dob,$userid, $usertype) {
		if($this->getUser()->getUsertype()==3 || $this->getUser()->getUserid()==$userid) {
			$usertoupdate=new User();
			$usertoupdate->getUserById($userid);
			if($this->getUser()->getUsertype()!=3) {
				$usertype="";
			}
			$result=$usertoupdate->updateUser($username,$firstname,$surname,$password,$email,$dob,$usertype, $userid);
			return $result;
			
		}
	}

	public function registerUser($userid,$username,$userpass,$firstname,$surname,$email,$dob, $vKey) {
		$reguser=new User();
		$result=$reguser->registerUser($userid,$username,$userpass,$firstname,$surname,$email,$dob, $vKey);
		if($result['insert']==1) {
			$this->loginDiscreet($username, $userpass);
			// send verification email
			$emailTo = $email;
			$subject = "getwhisky email verification";
			$message = "<h1>Thank you for registering with getwhisky</h1><p>Please click on the link below to verify your account!</p><a href='http://ecommercev2/verify.php?vkey=$vKey'>Verify account</a>";
			$headers = "From: neilunidev@yahoo.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($emailTo, $subject, $message, $headers);
		}
		return $result;
	}

	public function resendValidationEmail() {
		if ($this->getUser()) {
			$emailTo = $this->getUser()->getEmail();
			$vKey = $this->getUser()->getVerificationKey();
			$subject = "getwhisky email verification";
			$message = "<h1>Thank you for registering with JA Mackay</h1><p>Please click on the link below to verify your account!</p><a href='http://ecommercev2/verify.php?vkey=$vKey'>Verify account</a>";
			$headers = "From: neilunidev@yahoo.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$result['sent'] = mail($emailTo, $subject, $message, $headers);
			$result['address'] = $emailTo;
		} else {
			$result = false;
		}
		return $result;
	}


	public function sendPasswordResetEmail($email) {
		$userCRUD = new UserCRUD();
		$found = $userCRUD->getUserByEmail($email);

    	if ($found) {
			$uniqueIdGenerator = new UniqueIdGenerator("passwordResetKey");
        	$resetKey = $uniqueIdGenerator->getUniqueId();
			$userCRUD->setResetKeyByEmail($resetKey, $email);
			// send email with reset key
			$subject = "getwhisky password reset";
			$message = "<p>A password reset has been requested to this email address, please follow the following link to reset your password.</p><a href='http://ecommercev2/password-reset.php?resetKey=$resetKey'>reset password</a>";
			$message.= "<p>If you did not request this change <a href='http://ecommercev2/password-reset.php?resetKey=$resetKey&cancel=1'>click here</a> to cancel";
			$headers = "From: neilunidev@yahoo.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($email, $subject, $message, $headers);
			return 1;
		} else {
			return 0;
		}
	}

	/*****************
	 * Resets a user's password where the user's reset token matches
	 * the passed in reset token. Once the password has been reset
	 * the reset token is wiped.
	 *******************************************************/
	public function resetUserPassword($resetKey, $plaintext) {
		$userHash = new UserHash();
		$userCRUD = new UserCRUD();
		$userHash->newHash($plaintext);
		$hashedPassword = $userHash->getHash();
		$result = $userCRUD->updateUserPassword($hashedPassword, $resetKey);
		if ($result) {
			// Remove the password reset token
			$result = $userCRUD->wipeResetKeyWithNewPass($hashedPassword);
			return $result;
		}
	}
	

	/******************************
	 * GENERAL PAGE DISPLAY METHODS
	 ***************************/

	 // returns the site-wide shared <head> content
    public function displayHead() {
		$html= "
				<meta charset='UTF-8'>
				<meta http-equiv='X-UA-Compatible' content='IE=edge'>
				<meta name='viewport' content='width=device-width, initial-scale=1.0'>
				<!-- JQuery, FontAwesome -->
				<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
				<script src='https://kit.fontawesome.com/1942d39d14.js' crossorigin='anonymous'></script>
				<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
				<!-- GoogleFonts -->
				<link rel='preconnect' href='https://fonts.googleapis.com'>
				<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
				<link href='https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap' rel='stylesheet'>
				<link href='https://fonts.googleapis.com/css2?family=Courier+Prime&display=swap' rel='stylesheet'>
				<link href='https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap' rel='stylesheet'>
				<!-- CSS -->
				<link rel='stylesheet' href='style/css/reset.css'>
				<link rel='stylesheet' href='style/css/style.css'>
				<link rel='stylesheet' href='style/css/alerts.css'>
				";
		return $html;
	}

	// Returns the site-wide <header> section
	public function displayHeader() {
		$html="";
		$html.="<header>";
			$html.="<div class='page-overlay'></div>";
			$html.="<div class='menu-overlay'></div>";
			$html.="<div class='header-content'>";
				$html.="<a href='/index.php'><img class='header-logo' src='assets/getwhisky-logo-lowercase.png' alt=''></a>";
					$html.="<nav class='header-menu'>";
						$html.="<ul>";					
							$html.="<li><a href='/cart.php'><i class='header-nav-icon fas fa-shopping-basket'><span class='cart-count'>".$this->getCart()->getCartItemCount()."</span></i></a><a class='header-nav-link' href='/cart.php'>basket</a></li>";
							if ($this->getUser()->getUsertype() == 0) {
								$html.="<li><a class='header-nav-link' href='/login.php'>Sign in</a></li>";
							}
							if ($this->getUser()->getUsertype() == 3) {
								$html.="<li><a class='header-nav-link' href='/admin.php'>Admin</a></li>";
							}
							if ($this->getUser()->getUsertype() >= 2) {
								$html.="<li><a href='/user.php'><i class='header-nav-icon fas fa-user'></i></a><a class='header-nav-link' href='/user.php'>Account</a></li>";
								$html.="<li><a class='header-nav-link' href='/logout.php'>Logout</a></li>";
							}
							if ($this->getUser()->getUsertype() == 1) {
								$html.="<li><a class='header-nav-link' href='/suspended.php'>Account</a></li>";
								$html.="<li><a class='header-nav-link' href='/logout.php'>Logout</a></li>";
							}
							$html.="<li><i class='fas fa-bars product-menu-button' id='product-menu-button'></i></li>";
						$html.="</ul>";
				$html.="</nav>";
			$html.="</div>";
		$html.="</header>";
		return $html;
	}

	public function displayCartNotifications() {
		$html = "";
		if (isset($_SESSION['cart-update-notification'])) {
            $html.=$_SESSION['cart-update-notification'];
            unset($_SESSION['cart-update-notification']);
        }
		return $html;
	}

	/***************************************************************************************************************************************************
	 * Product methods
	 ***********************************************************/
	public function displayProductMenu() {
		$html = "";
		$html.="<nav id='product-menu-container' class='product-menu-container'>";
		$html.= $this->getProductMenu();
		$html.="</nav>";
		return $html;
	}

	public function getProductsFromDatabase() {
		$source = new ProductCRUD();
		$products = $source->getProducts();
		if(count($products)>0) {
			$haveProducts=count($products);
			foreach($products as $product) {
				$newProduct=new Product($product);
				array_push($this->products, $newProduct);
			}
		} 
	}

	public function getCategoryFilters($categoryId) { 
		$this->productCategoryFilters = new ProductFilter($categoryId);
		return $this->productCategoryFilters; 
	}

	public function displayCategoryDetails($categoryId) {
		$source = new MenuCRUD();
		$details = $source->getCategoryDetails($categoryId);
		$html = "";
		foreach($details as $detail) {
			$html.="<h2>".$detail['name']."</h2>";
			$html.="<p>".$detail['description']."</p>";
		}
		return $html;
	}

	/**********************************************************************************************
	 * Cart methods
	 ****************************************************************************/

	/********************
	 * Method checks whether a user has an existing cart on the database and retrieves it
	* If no cart exists a new cart is created on the database and the function recalled
	* to retrieve the newly created cart.
	*********************/
	public function initializeUserCart() {
	$haveCart = 0;
	$source = new CartCRUD();
	$haveCart = $source->getUserCart($this->getUser()->getUserid());
	if ($haveCart) {
		// Existing cart found on database create cart instance in page
		$this->setCart(new Cart($haveCart[0]));
	} else {
		// create new cart on database
		$uniqueIdGenerator = new UniqueIdGenerator("cart_id");
		$cartId = $uniqueIdGenerator->getUniqueId();
		$newCart = $source->createNewCart($cartId, $this->getUser()->getUserid());
		if ($newCart) {
			// If creating cart was successful recall function to initialize the cart
			$this->initializeUserCart();
		} else {
			// Failed, handle errors.
		}
	}
	}

	public function displayCart() {
		return $this->getCart();
	}

	public function addToCart() {

	}

	/********************************************************
 	* Updates the stock of an item in the cart
	* Does an initial check for the product stock to see whether there is enough stock
	* If there is sufficient stock it calls the updateItemQuantity function inside the cart
	* Returns various results depending on state
	* 	0	-	General fail
	* 	1	-	Update success
	* 	2	-	Insufficient stock to update quantity
	******************************/
	public function updateCartItemQuantity($productId, $quantity) {
		// Guard clause to prevent 0 quantity being submitted
		if ($quantity <= 0) return 0;

		// check for sufficient stock
		$stock = 0;
		foreach($this->getProducts() as $product) {
			if ($product->getId() == $productId) $stock = $product->getStock();
		}

		if ($quantity <= $stock) {
		// stock ok - update cart quantity to passed quantity
		$result = $this->getCart()->updateCartItemQuantity($productId, $quantity);
		} else {
		// insufficient stock
		$result = 2;
		}
	
		return $result;
	}

	public function removeFromCart($productId) {
		$result = $this->getCart()->removeFromCart($productId);
		return $result;
	}
}
?>
