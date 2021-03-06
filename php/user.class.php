<?php
// The User class, responsible for user data, checks that the altered user data is valid before changing the 
// data in the temporary instance of that user. It then sends that data to the UserCRUD class.
require_once("util.class.php");
require_once("usercrud.class.php");
require_once("userhash.class.php");
require_once("user-address.class.php");
require_once("unique-id-generator.class.php");
require_once("order.class.php");

class User {
    private $userid;
	private $userhash; 
	private $firstname;
    private $surname; 
	private $lastsession; 
	private $email; 
	private $usertype;
    private $vKey;
    private $verified;
	private $addresses = [];
	private $orders = [];
	
	public function __construct() {
		$this->userid=-1;
		$this->usertype=0;
        $this->verified=false;
		$this->userhash=new UserHash(); // when user is constructed an instance of userHash is also
										// constructed and assigned to the userhash field
	}
	
	private function setUserid($userid) {
		$this->userid=$userid;
	}

	public function setGuestUserid() {
		if ($_SESSION['guest'] == true) {
			$this->setUserid($_SESSION['userid']);
		}
	}
	

	// checks for validation via the util class and sends an error message if firstname
	// does not meet criteria
	private function setFirstname($firstname) {
		$message="";
		if(util::valStr($firstname)) {
			$this->firstname=$firstname;
		} else {$message="Invalid Firstname<br />";}
		return $message;
	}

	
	private function setSurname($surname) {
		$message="";
		if(util::valStr($surname)) {
			$this->surname=$surname;
		} else { $message="Invalid Surname<br />";}
		return $message;
		
	}
	
	private function setEmail($email) {
		$message="";
		if (util::valEmail($email)) {
			$this->email=$email;
		} else {$message="Invalid Email Address<br />";}
		return $message;
		
	}

	private function setSession($session) {
		$this->lastsession=$session;
	}
	
	private function setUsertype($usertype) {
		$this->usertype=$usertype;
	}

    private function setVerified($verified) {
        $this->verified = $verified;
    }

	private function setVerificatinKey($vKey) {
		$this->vKey = $vKey;
	}

	public function getUserid() { return $this->userid; }
	public function getFirstname() { return $this->firstname; }
	public function getSurname() { return $this->surname; }
	public function getEmail() { return $this->email; }
	public function getSession() { return $this->lastsession; }
	public function getUsertype() { return $this->usertype; }
    public function getVerifiedStatus() { return $this->verified; }
	public function getVerificationKey() { return $this->vKey; }
	public function getAddresses() { return $this->addresses; }
	public function getOrders() { return $this->orders; }

        /*  
            UserCRUD class is used to get the data. It is checked that a single record is returned
            method should return either 0 records, if the email is not found, or 1 record if the
            email is found.
            If a single record is returned the setters are used to set the values of the different 
            fields for the class.
        */
        public function getUserByEmail($email) {
            $haveuser=false;
            $source=new UserCRUD();
            $data=$source->getUserbYEmail($email);
            if(count($data)==1) {
                $user=$data[0];
                $this->setUserid($user["userid"]);
                $this->setFirstname($user["firstname"]);
                $this->setSurname($user["surname"]);
                $this->setSession($user["lastsession"]);
                $this->setEmail($user["email"]);
				$this->setUsertype($user["usertype"]);
                $this->setVerified($user["verified"]);
				$this->setVerificatinKey($user["vkey"]);
				$this->userhash->initHash($user["userpass"]);
                $haveuser=true;
            } 
            return $haveuser;
		}
		
		// takes the details submitted by a user compares it to the details stored in the database
		public function authEmailPass($email, $userpass) {
			$authenticated=$this->getUserByEmail($email);
			if($authenticated) {
				$authenticated=$this->userhash->testPass($userpass);
			}
			return $authenticated;
		}

		public function testPassword($userpass) {
			$authenticated = $this->userhash->testPass($userpass);
			return $authenticated;
		}

		public function getUserById($userid) {
			$haveuser=false;
			$source=new UserCRUD();
			$data=$source->getUserById($userid);
			if(count($data)==1) {
				$user=$data[0];
				$this->setUserid($user["userid"]);
				$this->setFirstname($user["firstname"]);
				$this->setSurname($user["surname"]);
				$this->setSession($user["lastsession"]);
				$this->setEmail($user["email"]);
				$this->setUsertype($user["usertype"]);
                $this->setVerified($user["verified"]);
				$this->setVerificatinKey($user["vkey"]);
                $this->userhash->initHash($user["userpass"]);
				$haveuser=true;
			} 
			return $haveuser;
		}
	
		// gets the session sends it to the userCRUD class which sends the session to
		// the lastSession attribute in the user table
		public function storeSession($userid, $session="") {
			$result=0;
			$target=new UserCRUD();
			$result=$target->storeSession($userid, $session);
			if($result) {$this->setSession($session);}
			return $result;
		}
		
		// returns false if the new session is not the same as the user table session
		// This is what sets the values of the user
		public function authIdSession($id, $session) {
			$authenticated=false;
			$authenticated=$this->getUserById($id);
			if($authenticated) {
				if($this->getSession()!=$session) { $authenticated=false; }
			}
			return $authenticated;
		}
		
		// password setter
		// set up the instance of the UserHash class with a hash of a new plaintext password
		// returns an error message if the password is too weak
		private function setPass($password) {
			$message="";
			if($this->userhash->checkRules($password)) {
				$this->userhash->newHash($password);
			} else {
				$message="Password did not meet complexity standards<br />";
				$message.="Please enter a password between 8 and 72 characters<br />";
			}
			return $message;
		}
	
		
		// takes the number of parameters which comes from the form and after using setters for the
		// user, passes the parameters to a store method in the userCRUD class.
		// messages variable will be used to notify the user of any issues.
        // sends a verification key to the database but doesn't store that key as unnecessary.
		public function registerUser($userid, $password, $firstname,$surname, $email, $vKey) {
			$insert=0;
			$messages="";
			$target=new UserCRUD();
		
			$messages.=$this->setUserid($userid);
			$messages.=$this->setFirstname($firstname);
			$messages.=$this->setSurname($surname);
			$messages.=$this->setPass($password); // adds error message if pass doesn't meet standards
			$messages.=$this->setEmail($email);
			if($messages=="") {
				$insert=$target->storeNewUser($this->getUserid(), $this->getFirstname(),$this->getSurname(),$this->userhash->getHash(),$this->getEmail(), $vKey);
				if($insert!=1) { $messages.=$insert;$insert=0; }
			}
			$result=['insert' => $insert,'messages' => $messages];
			return $result;
		}

		// allows a user to update their details by calling the update method in the userCRUD class
		// also uses the util class to validate
		public function updateUser($firstname,$surname,$password,$email,$usertype, $userid) {		
			$update=0;
			$messages="";
			$found=$this->getUserById($userid);
			$target=new UserCRUD();
			if($found) {
				if(util::posted($firstname)){$messages.=$this->setFirstname($firstname);}
				if(util::posted($surname)){$messages.=$this->setSurname($surname);}
				if(util::posted($password)){$messages.=$this->setPass($password);}
				if(util::posted($email)){$messages.=$this->setEmail($email);}
				if(util::posted($usertype)){$messages.=$this->setUsertype($usertype);}
				if($messages=="") {
					$update=$target->updateUser($this->getFirstname(), $this->getSurname(), $this->userhash->getHash(), $this->getEmail(),$this->getUsertype(), $userid);
					if($update!=1) {$messages=$update;$update=0;}
				}			
			}
			if ($messages == "") $messages = "Details successfully updated";
			$result=['update' => $update, 'messages' => $messages];	
			return $result;
		}

		/****
		 * Retrieves user addresses from the database
		 * and populates the addresses obj array
		 ***********************/
		private function retrieveUserAddresses() {
			$source = new UserAddressCRUD();
			$addresses = $source->getUserAddresses($this->getUserid());
			foreach ($addresses as $address) {
				array_push($this->addresses, new UserAddress($address));
			}
		}

		public function getAndDisplayAddressPage() {
			$this->retrieveUserAddresses();
			$html = "";
			if ($this->getAddresses()) {
				// display addresses
				foreach($this->getAddresses() as $address) {
					$html.=$address;
				}
			} else {
				$html.="<p>You currently have no saved addresses.</p>";
				$html.="<p style='margin-bottom:40px;'>Click the button below to add a delivery address.</p>";
			}

			$html.="<div class='address-btn-container'>";
			
			$html.="<span class='wrapper-link' id='add-new-address-btn'></span>";
				$html.="<button>Add new address +</button>";
			$html.="</div>";

			return $html;
		}

		// Adds an address to the database via the UserAddressCRUD class
		public function addNewAddress($addressid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county) {
			$insert = new UserAddressCRUD();
			$result = $insert->addNewAddress($addressid, $this->getUserid(), $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county);
			return $result;
		}
	
		// Updates a user's address
		public function updateUserAddress($addressid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county) {
			$update = new UserAddressCRUD();
			$result = $update->updateUserAddress($addressid, $this->getUserid(), $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county);
			return $result;
		}

		// Deletes a user's address
		public function deleteUserAddress($addressid) {
			$delete = new UserAddressCRUD();
			$result = $delete->deleteUserAddress($addressid, $this->getUserid());
			return $result;
		}

		/****
		 * Retrieves user orders from the database and
		 * populates the orders Obj array with orders
		 *******************************/
		private function retrieveUserOrders() {
			$source = new OrderCRUD();
			$orders = $source->getUserOrders($this->getUserid());
			foreach($orders as $order) {
				array_push($this->orders, new Order($order));
			}
		}

		// Calls the function to retrieve order objects
		// and then displays them
		public function getAndDisplayOrderPage() {
			$this->retrieveUserOrders();
			$html = "";
			foreach($this->getOrders() as $order) {
				$html.=$order->displayUserOrderPage();
			}
			return $html;
		}

		public function displayAccountOptionsSidebar() {
			$html = "";
			$html.="<div class='account-sidebar'>";
				$html.="<h3 class='sidebar-header'>Account options</h3>";
					$html.="<div class='sidebar-content'>";
					$html.="<a href='/orders.php'>My orders</a>";
					$html.="<a href='/addresses.php'>My delivery addresses</a>";
					$html.="<a href='/userdetails.php'>My personal details</a>";
					$html.="<a href='/logout.php'>Sign out</a>";
				$html.="</div>";
			$html.="</div>";
			return $html;
		}

		// sends user menu data to the $ouput variable via the getters
		public function __toString() {
			$html = "";
			$html.="<div class='account-main-content'>";
				$html.="<div class='account-main-content-header'>";
					$html.="<h3>Hello ".$this->getFirstname()." ".$this->getSurname()."</h3>";
					$html.="<p>Welcome to your account page, select from one of the options below to manage your account.</p>";
				$html.="</div>";
				if ($this->getVerifiedStatus() == 0) {
					$html.="<div class='verification-container'>";
						$html.="<div class='verification-content'>";
							$html.="<p>Your account has not yet been verified</p>";
							$html.= "<button id='resend-validation'>Resend validation email</button>";
						$html.="</div>";
					$html.="</div>";
				}
				// Links to account options
				$html.="<div class='account-options'>";

					// Delivery addresses
					$html.="<div class='account-option'>";
						$html.="<div class='account-option-top'>";
							$html.="<div class='account-icon-container'>";
								$html.="<i class='fas fa-truck'></i>";
							$html.="</div>";
						$html.="</div>";
						$html.="<div class='account-option-bottom'>";
							$html.="<h4>Delivery Addresses</h4>";
							$html.="<p>Manage your addresses</p>";
						$html.="</div>";
						$html.="<a class='wrapper-link' href='/addresses.php'><span></span></a>";
					$html.="</div>";

					// Orders
					$html.="<div class='account-option'>";
						$html.="<div class='account-option-top'>";
							$html.="<div class='account-icon-container'>";
								$html.="<i class='fas fa-box-open'></i>";
							$html.="</div>";
						$html.="</div>";
						$html.="<div class='account-option-bottom'>";
							$html.="<h4>Your Orders</h4>";
							$html.="<p>View and manage your orders</p>";
							$html.="<a class='wrapper-link' href='/orders.php'><span></span></a>";
						$html.="</div>";
					$html.="</div>";

					// Account details
					$html.="<div class='account-option'>";
						$html.="<div class='account-option-top'>";
							$html.="<div class='account-icon-container'>";
								$html.="<i class='fas fa-user'></i>";
							$html.="</div>";
						$html.="</div>";
						$html.="<div class='account-option-bottom'>";
							$html.="<h4>Account Details</h4>";
							$html.="<p>Manage your personal details</p>";
							$html.="<a class='wrapper-link' href='/userdetails.php'><span></span></a>";
						$html.="</div>";
					$html.="</div>";


				$html.="</div>";
			$html.="</div>";
			return $html;
		}
	
    }
    
?>