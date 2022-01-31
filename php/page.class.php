<?php
//The Page class, responsible for the application state, checks that the user is authorised 
require_once("constants.php");
require_once("user.class.php");
require_once("product-menu.class.php");
require_once("product.class.php");
require_once("product-filter.class.php");
require_once("cart.class.php");
require_once("unique-id-generator.class.php");
require_once("order.class.php");

/****************
 * TODO: 
 * transfer cart from guest user to registered user on login.
 * Insert guest userid's to the database temporarily to allow
 * storing of addresses and carts with cascade functionality
 * through a cronjob that deletes exhausted guest userids.
 */
class Page {
	private $user;
	private $pagetype;
	private $isauthenticated;
	private $productMenu;
	private $products = [];
	private $productCategoryFilters;
	private $cart;
	private $product;
	
	public function __construct($pagetype=0){
		session_start();
		$this->setPagetype($pagetype);
		$this->user = new User();
		
		$this->productMenu = new ProductMenu();
		$this->setStatus(false);
        $this->checkUser();		
		$this->initializeUserCart();
		$this->getProductsFromDatabase();
	}
	
	public function getProducts() { return $this->products; }
	public function getProductMenu() { return $this->productMenu;}
	public function getPagetype() { return $this->pagetype;}
	public function getStatus() { return $this->isauthenticated;}
	public function getUser() {return $this->user;}    
	public function getCart() { return $this->cart; }
	public function getProduct() { return $this->product; }

	private function setPagetype($pagetype) {$this->pagetype=(int)$pagetype;}
	private function setStatus($status) {$this->isauthenticated=(bool)$status;}
	private function setCart($cart) { $this->cart = $cart; }
	private function setProduct($product) { $this->product = $product; }
	
	// Checks for a user in the $_SESSION
	// if session is found set status is set to true and
	// the authIdSession retrieves the user details and stores them
	// in the user class
	public function checkUser() {
		// Establish guest session
		if (!isset($_SESSION['userid'])) {
			$_SESSION['userid'] = "guest-".md5(time(). bin2hex(random_bytes(10)));
			$_SESSION['guest'] = true;
			// Insert guest to database
			// delete exhausted guest IDs weekly via cronjob?
			// update the database every page with the activity time
			// ensure everything requiring userids is set to cascade
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
	
	/**************
	 * Log user out if inactive for 1 hour 3200
	 ******************************/
	private function checkInactivityLength() {
		if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 64000) {
			$this->logout();
		}
		$_SESSION['last_activity'] = time();
	}

	/*******************
	 * Checks for user credentials with the authNamePass method
	 * if the hash of the passed in password matches the hash in the database
	 * the user is logged in and a session is stored in the database
	 **************************************************/
	public function login($email, $userpass, $checkoutLogin) {
		session_regenerate_id();
		if($this->getUser()->authEmailPass($email,$userpass)) {
			$this->getUser()->storeSession($this->getUser()->getUserid(),session_id());
			$useridAsGuest = $_SESSION['userid'];
			$_SESSION['guest'] = false;
			$_SESSION['userid']=$this->getUser()->getUserid();
			$_SESSION['last_activity'] = time(); // init inactivity timer

			// Transfer cart before redirect
			$this->transferCart($this->getUser()->getUserid(), $useridAsGuest);

			if ($checkoutLogin) {
				header("Location: checkout.php");
				exit();
			}
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

	/*******
	 * Transfers a cart from the guest-session to the user's account
	 * if the guest-session cart has items in it
	 *********************/
	private function transferCart($userid, $useridAsGuest) {
		if (count($this->getCart()->getItems()) > 0) {
			$this->getCart()->transferCart($userid, $useridAsGuest);
		}
	}

	/****************
	 * A single use function that logs the user in on the backend
	 * during the registration process
	 **************************************************/
	public function loginDiscreet($email, $userpass) {
		session_regenerate_id();
		if($this->getUser()->authEmailPass($email,$userpass)) {
			$this->getUser()->storeSession($this->getUser()->getUserid(),session_id());
			$useridAsGuest = $_SESSION['userid'];
			$_SESSION['userid']=$this->getUser()->getUserid();
			$_SESSION['guest'] = false;

			// transfer cart
			$this->transferCart($this->getUser()->getUserid(), $useridAsGuest);
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
	public function updateUser($username,$firstname,$surname,$password,$email,$userid, $usertype) {
		if($this->getUser()->getUsertype()==3 || $this->getUser()->getUserid()==$userid) {
			$usertoupdate=new User();
			$usertoupdate->getUserById($userid);
			if($this->getUser()->getUsertype()!=3) {
				$usertype="";
			}
			$result=$usertoupdate->updateUser($username,$firstname,$surname,$password,$email,$usertype, $userid);
			return $result;
			
		}
	}

	public function registerUser($userid,$userpass,$firstname,$surname,$email, $vKey) {
		$reguser=new User();
		$result=$reguser->registerUser($userid,$userpass,$firstname,$surname,$email, $vKey);
		if($result['insert']==1) {
			$this->loginDiscreet($email, $userpass);
			// send verification email
			$emailTo = $email;
			$subject = "getwhisky email verification";
			$message = "<h1>Thank you for registering with getwhisky</h1><p>Please click on the link below to verify your account!</p><a href='http://getwhisky/verify.php?vkey=$vKey'>Verify account</a>";
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
			$message = "<h1>Thank you for registering with JA Mackay</h1><p>Please click on the link below to verify your account!</p><a href='http://getwhisky/verify.php?vkey=$vKey'>Verify account</a>";
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
			$message = "<p>A password reset has been requested to this email address, please follow the following link to reset your password.</p><a href='http://getwhisky/password-reset.php?resetKey=$resetKey'>reset password</a>";
			$message.= "<p>If you did not request this change <a href='http://getwhisky/password-reset.php?resetKey=$resetKey&cancel=1'>click here</a> to cancel";
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
				<link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap' rel='stylesheet'>
				<!-- CSS -->
				<link rel='stylesheet' href='style/css/reset.css'>
				<link rel='stylesheet' href='style/css/style.css'>
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
				$html.="<a href='/index.php' id='getwhisky-logo-link'><img class='header-logo' alt='getwhisky logo - link to homepage' src='assets/getwhisky-logo-lowercase.png' alt=''></a>";
				$html.="<nav class='header-menu'>";
					$html.="<ul>";					
						if ($this->getCart()) {
							$html.="<li><a href='/cart.php'><i class='header-nav-icon fas fa-shopping-basket'><span class='cart-count'>".$this->getCart()->getCartItemCount()."</span></i></a><a class='header-nav-link' href='/cart.php'>basket</a></li>";
						} else {
							$html.="<li><a href='/cart.php'><i class='header-nav-icon fas fa-shopping-basket'></i></a><a class='header-nav-link' href='/cart.php'>basket</a></li>";
						}
						if ($this->getUser()->getUsertype() == 0) {
							$html.="<li><a href='/login.php'><i class='header-nav-icon fas fa-user'></i></a><a class='header-nav-link' href='/login.php'>Sign in</a></li>";
						}
						if ($this->getUser()->getUsertype() == 3) {
							$html.="<li><a class='header-nav-link' href='/admin.php'>Admin</a></li>";
						}
						if ($this->getUser()->getUsertype() >= 2) {
							$html.="<li><a href='/user.php'><i class='header-nav-icon fas fa-user'></i></a><a class='header-nav-link' href='/user.php'>Account</a></li>";
						}
						if ($this->getUser()->getUsertype() == 1) {
							$html.="<li><a class='header-nav-link' href='/suspended.php'>Account</a></li>";
							$html.="<li><a class='header-nav-link' href='/logout.php'>Logout</a></li>";
						}
						$html.="<li><i class='fas fa-bars product-menu-button' id='product-menu-button'></i></li>";
					$html.="</ul>";
				$html.="</nav>";

				$html.="<div class='search-bar-container'>";
					$html.="<input type='text' placeholder='Looking for something?' id='product-search-bar' class='product-search-bar'/>";
					$html.="<i class='fas fa-search' id='search-icon'></i>";
					$html.="<div id='search-results'></div>";
				$html.="</div>";
			$html.="</div>";
		$html.="</header>";
		return $html;
	}

	
	/***************************************************************************************************************************************************
	 * Product methods
	 **************************************************************************************************************************************************/
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

	public function setPageProductById($productId) {
		$found = 0;
		foreach($this->getProducts() as $product) {
			if ($product->getId() == $productId) {
				$this->setProduct($product);
				$found = 1;
				break;
			}
		}
		return $found;
	}

	public function displayProductPage() {
		if ($this->getProduct()->isActive()) return $this->getProduct()->displayProductPage();
		else
		$html = "";
		$html.="<div id='product-unavailable' style='margin: auto;text-align:center;'>";
			$html.= "<h3>Sorry this product is no longer available!</h3>";
			$html.="<img style='width:200px;display:block;margin:auto;' src='../assets/product-images/no-products-found.jpg'>";
			$html.="<p>Why not have a look at some other similar products?</p>";
		$html.="</div>";
		return $html;
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
				return $haveCart;
			}
		}
		return $haveCart;
	}

	public function displayCart($lastViewdProductid = null) {
		$html = "";
		$html.= $this->getCart()->displayCart($lastViewdProductid);
		$html.=$this->displayFeaturedProductsOwl("Why not try some of the getwhisky favourites?");
		return $html;

	}

	/***********
	 * Displays any cart notifications that have been
	 * saved to the session by the cart class
	 *******************************/
	public function displayCartNotifications() {
		$html = "";
		if (isset($_SESSION['cart-update-notification'])) {
            $html.=$_SESSION['cart-update-notification'];
            unset($_SESSION['cart-update-notification']);
        }
		return $html;
	}


	public function addToCart($productId, $quantity=1) {
		$result = $this->getCart()->addToCart($productId, $quantity);
		return $result;
	}

	public function updateCartItemQuantity($productId, $quantity) {
		// Guard clause to prevent 0 quantity being submitted
		if ($quantity <= 0) return 0;
		$result = $this->getCart()->updateCartItemQuantity($productId, $quantity);
		return $result;
	}

	public function removeFromCart($productId) {
		$result = $this->getCart()->removeFromCart($productId);
		return $result;
	}


	/************************************************************************************************
	 * ORDER CREATION METHODS
	 ***********************************************************************************************/

	/*********
	 * Creates an order after a payment has been received
	 * the arguments should only be supplied by first retrieving
	 * them from a page object on the session, never from a form, or on a webpage
	 * validation of address takes place in the create-checkout-session file
	 * 
	 * THIS METHOD IS NOT ATTACHED TO THE MAIN USER SESSION	AND SHOULD
	 * ONLY BE CALLED THROUGH THE STRIPE PAYMENT HANDLING WEBHOOK
	 ****************************/
	public function createOrder($cartid, $addressid, $userid, $deliveryLabel, $deliveryCost, $stripePaymentIntent, $email) {
		$result = 0;
		// Retrive a temporary instance of the user's cart
		// and check out from the cart object
		$cartCRUD = new CartCRUD();
		$tmpUserCart = $cartCRUD->getUserCart($userid);
		$this->setCart(new Cart($tmpUserCart[0]));
		

		
		// Create unique order id
		$uniqueIdGenerator = new UniqueIdGenerator("order_id", 5);
		$orderid = $uniqueIdGenerator->getUniqueId();
		// Get order datetime
		$date = new DateTime();
		$dateTimeAdded = $date->format("Y-m-d H:m:s");

		// Create order on database
		$orderCRUD = new OrderCRUD();
		$orderCRUD->createOrder($orderid, $userid, $addressid, $deliveryLabel, $deliveryCost, $stripePaymentIntent, $dateTimeAdded, ($this->getCart()->getCartTotal() + $deliveryCost));

		// add order items to order and update stock levels
		$productCRUD = new ProductCRUD();
		foreach ($this->getCart()->getItems() as $cartItem) {
			$orderCRUD->addToOrder($orderid, $cartItem->getProductId(), $cartItem->getQuantity(), $cartItem->returnCorrectItemPrice());
			$productCRUD->decreaseStockByQuantity($cartItem->getProductId(), $cartItem->getQuantity());
		}		
		
		// Checkout cart
		$this->getCart()->checkOutCart();

		$this->sendOrderConfirmationEmail($orderid, $email);
	}


	/*************
	 * Sends details of a created order as an email to the user's
	 * email address
	 ***************************************/
	private function sendOrderConfirmationEmail($orderid, $email) {
		$orderCRUD = new OrderCRUD();
		$addressCRUD = new UserAddressCRUD();

		$orderDetails = $orderCRUD->getOrderById($orderid);
		$orderItems = $orderCRUD->getOrderItems($orderid);
		$addressDetails = $addressCRUD->getUserAddressById($orderDetails[0]['address_id'], $orderDetails[0]['userid']);

		$html = "";
        $html.="<div style='margin:auto;font-family:sans-serif;'>";
            $html.="<p style='margin-left:10px;margin-right:10px;font-size:30px;'>Hi ".$addressDetails[0]['full_name']."</p>";
            $html.="<p style='margin-left:10px;margin-right:10px;font-size:30px;'>Thank you for your recent purchase on getwhisky.site.</p>";
            $html.="<p style='margin-left:10px;margin-right:10px;padding-bottom:60px;font-size:30px;'>Please find your order summary below.</p>";

            $html.="<div style='background-color:#ededed;padding:10px 20px 10px 20px;line-height:0.8;display:flex;justify-content:space-between'>";
                $html.="<div>";
                    $html.="<h3 style='font-size:18px;'>Order details</h3>";
                    $html.="<p style='font-size:14px;'><b>Order ID: </b>".$orderDetails[0]['order_id']."</p>";
                    $html.="<p style='font-size:14px;'><b>Payment ref: </b>".$orderDetails[0]['stripe_payment_intent']."</p>";
                    $html.="<p style='font-size:14px;'><b>Order date: </b>".date("d M Y",strtotime($orderDetails[0]['date_placed']))."</p>";
                    $html.="<p style='font-size:14px;'><b>Total: </b>&#163;".($orderDetails[0]['total']+$orderDetails[0]['delivery_paid'])."</p>";
                    $html.="<p style='font-size:14px;'><b>Delivery Option: </b>".$orderDetails[0]['delivery_label']." &#163;".$orderDetails[0]['delivery_paid']."</p>";
                $html.="</div>";
                $html.="<div style='padding:40px 0px 0px 0px;'>";
                    $html.="<h3 style='font-size:18px;'>Delivery address</h3>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['full_name']."</p>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['line1']."</p>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['line2']."</p>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['postcode']."</p>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['city']."</p>";
                    $html.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['county']."</p>";
                $html.="</div>";
            $html.="</div>";

            $html.="<div style='background-color:#ededed;padding:40px 20px 10px 20px;line-height:0.8;'>";
                $html.="<h3 style='font-size:18px;padding:0;margin:0;padding-bottom:20px;'>Items to be delivered</h3>";
                foreach($orderItems as $item) {
                    $html.="<div style='display:flex;justify-content:space-between;border-bottom:1px solid black;'>";
                        $html.="<div>";
                            $html.="<p style='font-size:14px;font-weight:600;'>".$item['name']."</p>";
                            $html.="<p style='opacity:0.8;font-size:14px;'>Quantity: ".$item['quantity']."<p>";
                            $html.="<p style='opacity:0.8;font-size:14px;'>Price unit: &#163;".$item['price_bought']."</p>";
                        $html.="</div>";

                        $html.="<div style='display:flex;align-items:flex-end;'>";
                            $html.="<p style='font-size:14px;font-weight:600;'> &#163;".($item['quantity'] * $item['price_bought'])."</p>";
                        $html.="</div>";
                    $html.="</div>";
                }
            $html.="</div>";

            $html.="<div style='background-color:#ededed;padding:40px 20px 10px 20px;line-height:1.2;'>";
                $html.="<p style='font-size:14px;opacity:0.8;'>If you have any issues with your order please give us a call on 011114411. Alternatively email us at info@getwhisky.com</p>";
                $html.="<p style='font-size:14px;opacity:0.8;'>These details can be viewed through the orders section on your account if you have created an account with us. If not please consider registering with us <a href='http://ecommercev2/register.php'>here!</a></p>";
            $html.="</div>";
        $html.="</div>";


			$subject = "getwhisky order confirmation #".$orderDetails[0]['order_id']."";
			$message = $html;
			$headers = "From: neilunidev@yahoo.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$result = mail($email, $subject, $message, $headers);
			error_log($email,0);
			error_log($result, 0);
	}





	/*****************************************************************************************************************************************
	 * PRODUCT PROMOTION METHODS
	 * e.g. Featured products, discounted products
	 *****************************************************************************************************************************************/


	 /********
	  * Displays a carousel of featured products
	  * Uses owl carousel
	  * Takes a heading as parameter and a page
	  *****/
	public function displayFeaturedProductsOwl($sectionHeading) {
		$html = "";
		$html.="<div class='featured-products'>";
			$html.="<div class='featured-products-header'>";
				$html.="<h2>$sectionHeading</h2>";
			$html.="</div>";
			$html.="<div class='owl-carousel owl-featured-products'>";
				foreach($this->getProducts() as $product) {
					if ($product->isFeatured() && $product->isActive()) {
						$html.=$product->displayProductOwlFeatured();
					}
				}
			$html.="</div>";
			$html.="<i class='owl-nav-left fas fa-chevron-left'></i>";
            $html.="<i class='owl-nav-right fas fa-chevron-right'></i>";
        $html.="</div>";

		return $html;
	}

/***********
	 * Uses array_intersect to search through the page's products array and find products with 
	 * multiple matching attributes to that of the product page's product attributes.
	 * If enough attributes match the product page product's attributes then it is displayed as a
	 * related product using the product.class owl product display. 
	 *************************************/
	public function displayRelatedProducts() {
		$html = "";
		$relatedProducts = [];
		if ($this->getProduct()) {
			$productPageAttributes = $this->getProduct()->getAttributes();
			$productPageProductid = $this->getProduct()->getId();

			foreach($this->getProducts() as $product) {
				if ($product->isActive()) {
					$haystack = $product->getAttributes();
					// if 2 or more attributes match, count the product as related
					if (count(array_intersect($haystack, $productPageAttributes)) >= 2 && $product->getId() != $productPageProductid) {
						array_push($relatedProducts, $product);
					}
				}
			}

			// Only display if multiple related
			if (count($relatedProducts) > 5) {
				$html.="<div id='related-products-root'>";
					$html.="<div class='related-products-header'>";
						$html.="<h3>You may also like</h3>";
					$html.="</div>";
					$html.="<div class='owl-carousel owl-featured-products'>";
						foreach($relatedProducts as $relatedProduct) {
							$html.=$relatedProduct->displayProductOwlFeatured();
						}
					$html.="</div>";
					$html.="<i class='owl-nav-left fas fa-chevron-left'></i>";
            		$html.="<i class='owl-nav-right fas fa-chevron-right'></i>";
				$html.="</div>";
			}
		}
		return $html;
	}

	/************
	 * Displays the static featured banner section on the homepage
	 *******************************/
	public function displayFeaturedBannerSection() {
		$html = "";

		$html.="<div class='featured-banner-section'>";
			$html.="<div class='featured-banner-left'>";
				$html.="<img src='/assets/product-images/pink-gin-hamper.jpg' alt=''>";
			$html.="</div>";

			$html.="<div class='featured-banner-right'>";
				$html.="<div class='featured-right-text-container'>";
					$html.="<h2>New Gin Hampers</h2>";
					$html.="<p>Ice & Fire gin hampers now available and make for the perfect gift or treat!</p>";
					$html.="<a href='#'>Browse Hampers</a>";
				$html.="</div>";
			$html.="</div>";
		$html.="</div>";

		return $html;
	}




	/*************************
	 * ADMIN METHODS
	 *****************************************/
	 
}
?>
