<?php
/*********
 * User address class
 * Really doesn't need a class but at this point
 * It's only for consistency's sake
 * Only function used here is the toString()
 *************************************/
require_once("useraddresscrud.class.php");
class UserAddress {
    private $id;
    private $userid;
    private $identifier;
    private $fullName;
    private $telephone;
    private $postcode;
    private $line1;
    private $line2;
    private $city;
    private $county;


    public function __construct($id, $userid, $identifier, $fullName, $telephone, $postcode, $line1, $line2, $city, $county){
        $this->setId($id);
        $this->setUserid($userid);
        $this->setIdentifier($identifier);
        $this->setFullName($fullName);
        $this->setTelephone($telephone);
        $this->setPostcode($postcode);
        $this->setLine1($line1);
        $this->setLine2($line2);
        $this->setCity($city);
        $this->setCounty($county);
    }

    private function setId($id) { $this->id = $id; }
    private function setUserid($userid) { $this->userid = $userid; }
    private function setIdentifier($identifier) { $this->identifier = $identifier; }
    private function setFullName($fullName) { $this->fullName = $fullName; }
    private function setTelephone($telephone) { $this->telephone = $telephone; }
    private function setPostcode($postcode) { $this->postcode = $postcode; }
    private function setLine1($line1) { $this->line1 = $line1; }
    private function setLine2($line2) { $this->line2 = $line2; }
    private function setCity($city) { $this->city = $city; }
    private function setCounty($county) { $this->county = $county; }

    

    public function getId(){ return $this->id; }
    public function getUserid(){ return $this->userid; }
    public function getIdentifier(){ return $this->identifier; }
    public function getFullName(){ return $this->fullName; }
    public function getTelephone(){ return $this->telephone; }
    public function getPostcode(){ return $this->postcode; }
    public function getLine1(){ return $this->line1; }
    public function getLine2(){ return $this->line2; }
    public function getCity(){ return $this->city; }
    public function getCounty(){ return $this->county; }



    public function __toString() {

    }
}