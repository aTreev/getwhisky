<?php 
    require_once("usercrud.class.php");

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
        private $UNIQUE_ID;         // final generated uid


        /**********************
         * Takes the type of id as string
         * valid arguments --
         *      userid
         *      productid
         *      cartid
         *****************************/
        public function __construct($idType) {
            switch(strtolower($idType)) {
                case "userid":      
                    $this->column = "userid";       
                    $this->retrieveUserIds();       
                break;

                case "productid":   
                    $this->column = "productid";    
                    //$this->retrieveProductIds();
                break;

                case "cartid":      
                    $this->column = "cartid";       
                    //$this->retrieveCartIds();
                break;

                case "vkey":
                    $this->column = "vkey";
                    $this->retrieveVerificationKeys();
                break;

                case "passwordResetKey":
                    $this->colum = "password_reset_key";
                    $this->retrievePasswordResetKeys();
                break;
            }
            $this->generateNewUniqueId();
        }

        /*****************************************************************************
         * Generates a unique id using the hexadecimal conversion of a random set of 20 bytes
         * loops through the retrievedIds array, using the column index, to check
         * if the id already exists in the table.
         * Once a unique id is generated the UNIQUE_ID instance variable is populated
         ****************************************************************************/
        private function generateNewUniqueId() {
            do {
                $uniqueId = bin2hex(random_bytes(20));
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
    }
