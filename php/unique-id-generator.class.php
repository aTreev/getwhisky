<?php 
    require_once("usercrud.class.php");
    require_once("cartcrud.class.php");
    require_once("useraddresscrud.class.php");

    class UniqueIdGenerator {
    /**********************************
     * Unique ID generator, used as an extra security measure,
     * specifically against sequential ID attacks.
     * Takes in the desired id type as a string and calls the correct function
     * depending on argument.
     * Automatically sets the column name as required for the table.
     * UID retrieved by calling the getUniqueId function after instantiation.
     **********************************************************/
        
        private $retrievedIds = []; // Array of retrieved Ids
        private $column;            // Column index, used for iterating through id array
        private $length;            // Length of the uid
        private $UNIQUE_ID;         // final generated uid


        /**********************
         * Takes the type of id as string
         * valid arguments --
         *      userid
         *      productid
         *      cartid
         *****************************/
        public function __construct($idType, $length=20) {
            $this->setIdLength($length);

            switch(strtolower($idType)) {
                case "userid":      
                    $this->column = "userid";       
                    $this->retrieveUserIds();       
                break;

                case "cart_id":      
                    $this->column = "id";       
                    $this->retrieveCartIds();
                break;

                case "vkey":
                    $this->column = "vkey";
                    $this->retrieveVerificationKeys();
                break;

                case "passwordResetKey":
                    $this->column = "password_reset_key";
                    $this->retrievePasswordResetKeys();
                break;

                case "address_id":
                    $this->column = "address_id";
                    $this->retrieveAddressIds();
                break;

                case "order_id":
                    $this->column = "order_id";
                    $this->retrieveOrderIds();
                break;
            }
            $this->generateNewUniqueId();
        }


        private function setIdLength($length) { $this->length = $length; }
        private function getLength() { return $this->length; }
        
        /*****************************************************************************
         * Generates a unique id using the hexadecimal conversion of a random set of 20 bytes
         * loops through the retrievedIds array, using the column index, to check
         * if the id already exists in the table.
         * Once a unique id is generated the UNIQUE_ID instance variable is populated
         ****************************************************************************/
        private function generateNewUniqueId() {
            do {
                $uniqueId = bin2hex(random_bytes($this->getLength()));
                $unique = 1;

                foreach($this->retrievedIds as $id) {
                    if ($uniqueId == $id[$this->column]) {
                        $unique = 0;
                        break;
                    }
                }
            } while ($unique == 0);

            $this->UNIQUE_ID = $uniqueId;
        }


        // Returns the unique id
        public function getUniqueId(): string {
            return $this->UNIQUE_ID;
        }


        /******************************
         * RETRIEVAL METHODS
         *******************************/

        // Gets user ids from the database
        private function retrieveUserIds() {
            $source = new UserCRUD();
            $this->retrievedIds = $source->getUserIds();
        }

        // Get existing verification keys from the database
        private function retrieveVerificationKeys() {
            $source = new UserCRUD();
            $this->retrievedIds = $source->getExistingVerificationKeys();
        }

        private function retrievePasswordResetKeys() {
            $source = new UserCRUD();
            $this->retrievedIds = $source->getExistingPasswordResetKeys();
        }

        private function retrieveCartIds() {
            $source = new CartCRUD();
            $this->retrievedIds = $source->getExistingCartIds();
        }

        private function retrieveAddressIds() {
            $source = new UserAddressCRUD();
            $this->retrievedIds = $source->getExistingAddressIds();
        }

        private function retrieveOrderIds() {
            $source = new OrderCRUD();
            $this->retrieveIds = $source->getExistingOrderIds();
        }
    }
