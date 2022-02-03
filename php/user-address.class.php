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


    public function __construct($address){
        //$address['address_id'], $address['userid'], $address['identifier'], $address['full_name'], $address['telephone'], $address['postcode'], $address['line1'], $address['line2'], $address['city'], $address['county']
        $this->setId($address['address_id']);
        $this->setUserid($address['userid']);
        $this->setIdentifier($address['identifier']);
        $this->setFullName($address['full_name']);
        $this->setPhoneNumber($address['telephone']);
        $this->setPostcode($address['postcode']);
        $this->setLine1($address['line1']);
        $this->setLine2($address['line2']);
        $this->setCity($address['city']);
        $this->setCounty($address['county']);
    }

    private function setId($id) { $this->id = $id; }
    private function setUserid($userid) { $this->userid = $userid; }
    private function setIdentifier($identifier) { $this->identifier = $identifier; }
    private function setFullName($fullName) { $this->fullName = $fullName; }
    private function setPhoneNumber($telephone) { $this->telephone = $telephone; }
    private function setPostcode($postcode) { $this->postcode = $postcode; }
    private function setLine1($line1) { $this->line1 = $line1; }
    private function setLine2($line2) { $this->line2 = $line2; }
    private function setCity($city) { $this->city = $city; }
    private function setCounty($county) { $this->county = $county; }

    

    public function getId(){ return $this->id; }
    public function getUserid(){ return $this->userid; }
    public function getIdentifier(){ return $this->identifier; }
    public function getFullName(){ return $this->fullName; }
    public function getPhoneNumber(){ return $this->telephone; }
    public function getPostcode(){ return $this->postcode; }
    public function getLine1(){ return $this->line1; }
    public function getLine2(){ return $this->line2; }
    public function getCity(){ return $this->city; }
    public function getCounty(){ return $this->county; }


    public function displayAddressShort() {
        $html = "";

        $html.= "<p>".$this->getFullName()."</p>";
        $html.= "<p>".$this->getLine1()."</p>";
        if ($this->getLine2()) $html.= "<p>".$this->getLine2()."</p>";
        $html.= "<p>".$this->getPostcode()."</p>";
        $html.= "<p>".$this->getCity()."</p>";
        if ($this->getCounty()) $html.= "<p>".$this->getCounty()."</p>";
        if ($this->getPhoneNumber()) $html.= "<p>".$this->getPhoneNumber()."</p>";

        return $html;
    }

    public function __toString() {
        $html = "";
        $html.="<div class='address-item' id='".$this->getId()."'>";

            $html.="<div class='address-identifier-container'>";
                $html.="<p class='address-identifier'>".$this->getIdentifier()."</p>";
            $html.="</div>";

            $html.="<div class='address-details-container'>";
                $html.="<p>";
                $html.=$this->getFullName();
                $html.=", ".$this->getLine1();
                if ($this->getLine2()) {
                    $html.=", ".$this->getLine2();
                }
                $html.=", ".$this->getCity();
                if ($this->getCounty()) {
                    $html.=", ".$this->getCounty();
                }
                $html.=", ".$this->getPostcode();
                $html.="</p>";
            $html.="</div>";

            $html.="<div class='address-options-container'>";
                $html.="<input type='button' class='link-button' id='edit-".$this->getId()."' value='edit' />";
                $html.="<input type='button' class='link-button' id='delete-".$this->getId()."' value='delete' /'>";
            $html.="</div>";
        $html.="</div>";
        
        $html.="<div class='address-item-edit-form' id='address-item-edit-form-".$this->getId()."'>";
                $html.="<form class='form-inline' style='max-width:100%;'>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Address Name</label><input type='text' class='form-item' autocomplete='no' maxlength=50 id='edit-identifier-".$this->getId()."' value='".$this->getIdentifier()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Recipient full name</label><input type='name' class='form-item' maxlength=90 id='edit-full-name-".$this->getId()."' value='".$this->getFullName()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Phone number</label><input type='tel' class='form-item'  maxlength=12 id='edit-mobile-".$this->getId()."' value='".$this->getPhoneNumber()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Postcode</label><input type='postcode' class='form-item'  maxlength=10 id='edit-postcode-".$this->getId()."' value='".$this->getPostcode()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Address Line 1</label><input type='street' class='form-item'  maxlength=80 id='edit-line1-".$this->getId()."' value='".$this->getLine1()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<label>Address Line 2</label><input type='street' class='form-item'  maxlength=80 id='edit-line2-".$this->getId()."' value='".$this->getLine2()."' /><span></span>";
                    $html.="</div>";
                    $html.="<div class='input-container-50'>";
                        $html.="<label>City<input type='city' class='form-item-50'  maxlength=50 id='edit-city-".$this->getId()."' value='".$this->getCity()."' /></label>";
                        $html.="<label>County<input type='county' class='form-item-50'  maxlength=50 id='edit-county-".$this->getId()."' value='".$this->getCounty()."' /></label>";
                    $html.="</div>";
                    $html.="<div class='input-container-100'>";
                        $html.="<button type='submit' id='edit-submit-".$this->getId()."'>Submit</button>";
                    $html.="</div>";
                $html.="</form>";
        $html.="</div>";
        return $html;
    }
}