<?php
//The Page class, responsible for the application state, checks that the user is authorised 
//to carry out the update action and if so passes the data to a temporary instance of the User class.
require_once("user.class.php");
require_once("menu.class.php");
require_once("unique-id-generator.class.php");

class Page {
	private $user, $pagetype, $isauthenticated, $menu;
	
	public function __construct($pagetype=0){
		session_start();
		$this->setPagetype($pagetype);
		$this->user = new User();
		$this->setStatus(false);
        $this->checkUser();
	}
	
	public function getPagetype() { return $this->pagetype;}
	public function getStatus() { return $this->isauthenticated;}
	public function getUser() {return $this->user;}    
	private function setPagetype($pagetype) {$this->pagetype=(int)$pagetype;}
	private function setStatus($status) {$this->isauthenticated=(bool)$status;}
	
	// Checks for a user in the $_SESSION
	// if session is found set status is set to true and
	// the authIdSession retrieves the user details and stores them
	// in the user class
	public function checkUser() {
		// Establish guest session
		if (!isset($_SESSION['userid'])) {
			$_SESSION['userid'] = md5(time(). bin2hex(random_bytes(10)));
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
				<!-- CSS -->
				<link rel='stylesheet' href='style/css/reset.css'>
				<link rel='stylesheet' href='style/css/style.css'>
				<link rel='stylesheet' href='style/css/alerts.css'>
				";
		return $html;
	}

	// Builds the markup for the main nav menu
	public function buildHeaderMenu() {
		$html = "";
		$html.="<nav class='header-menu'>";
			$html.="<ul>";					
				$html.="<li><a href='/cart.php'><i class='header-nav-icon fas fa-shopping-basket'><span class='cart-count'>0</span></i></a><a class='header-nav-link' href='/cart.php'>basket</a></li>";
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
			$html.="</ul>";
		$html.="</nav>";
		return $html;
	}

	// Returns the site-wide <header> section
	public function displayHeader() {
		$html="";
		$html.="<div class='page-overlay'></div>";
		$html.="<div class='header-content'>";
			$html.="<a href='/index.php'><img class='header-logo' src='assets/getwhisky-logo-lowercase.png' alt=''></a>";
			$html.=$this->buildHeaderMenu();
		$html.="</div>";
	return $html;
	}
}
?>
