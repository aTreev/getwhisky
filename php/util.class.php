<?php
class util {
	
	/**********************
	* Removes html tags and trims spaces from a string
	* Returns true if string contains a value, false if it
	* does not
	* @param Mixed(String) $input
	* @return Boolean
	**********************/
	public static function valStr($input) {
		if($input==strip_tags((string)$input) && trim((string)$input)!="") {
			return true;
		} else {return false;}
	}
	
	/**********************
	* strips tags and removes end space from a string
	* @param Mixed(String) $input
	* @return String
	**********************/
	public static function sanStr($input) {
		return (trim(strip_tags((string)$input)));
	}
	
	/**********************
	* Validates an input to ensure that it is an integer
	* optionally tests to ensure integer is between set bounds
	* @param Mixed $input
	* @param optional $minmax array(Integer Min, Integer Max)
	* @return Boolean
	**********************/
	public static function valInt($input,$minmax=array(null,null)){
		try {
			$options=array("options" => array());
			if($minmax[0]!=null){$options["options"]["min_range"]=$minmax[0];}
			if($minmax[1]!=null){$options["options"]["max_range"]=$minmax[1];}
			if(filter_var($input, FILTER_VALIDATE_INT, $options) || filter_var($input, FILTER_VALIDATE_INT, $options)===0) {
				return true;
			} else { return false;}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**********************
	* Sanitises an input as an integer. If the input cannot
	* be cast as an integer, or is outwith the (optional) range
	* it will return 0
	* @param Mixed(int) $input
	* @param optional $minmax array(Integer Min, Integer Max)
	* @return Integer Defaults to 0
	**********************/
	public static function sanInt($input,$minmax=array(null,null)) {
		$returnvar=0;
		if(util::valInt($input,$minmax)) {
			$returnvar=(int)$input;
		}
		return $returnvar;
	}
	
	/**********************
	* Validates a variable as a floating point number, optionally validates
	* against a specific range
	* @param Mixed(float) $input
	* @param optional $minmax array(Float Min, Float Max)
	* @return Boolean
	**********************/
	public static function valFloat($input, $minmax=array(null,null)) {
		$valid=false;
		if(filter_var($input, FILTER_VALIDATE_FLOAT) || filter_var($input, FILTER_VALIDATE_FLOAT)===0.0) {
			if($minmax[0]==null && $minmax[1]==null){$valid=true;}
			if($minmax[0]!=null && $minmax[0]<=(float)$input){$valid=true;}
			if($minmax[1]!=null && $minmax[1]>=(float)$input){$valid=true;}
		}
		return $valid;
	}
	
	/*********************
	* Sanitises a floating point number, optionally checks against
	* a given range using the valFloat method
	* @param Mixed $input
	* @param optional $minmax array(Float Min, Float Max)
	* @return sanitised value
	*********************/
	public static function sanFloat($input,$minmax=array(null,null)) {
		$returnvar=0.0;
		if(util::valFloat($input,$minmax)) {
			$returnvar=(float)filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
		}
		return $returnvar;
	}
	
	/*********************
	* Validates a boolean, will return true if input is
	* 1, 'true', 'on', true, 'yes', 0, 'false', 'off'
	* 'no', false
	* @param Mixed $input
	* @return Mixed True, False, Null
	*********************/
	public static function valBool($input) {
		return filter_var($input, FILTER_VALIDATE_BOOLEAN);
	}
		
	/********************
	* Validates email addresses to RFC 822
	* @param String $input
	* @return Boolean
	********************/
	public static function valEmail($input) {
		if(filter_var($input, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
			return true;
		} else { return false;}
	}
	
	/********************
	* Strong email santisation, Remove all characters except 
	* letters, digits and !#$%&'*+-=?^_`{|}~@.[].
	* @param (String) $input
	* @return String Email address or null
	********************/
	public static function sanEmail($input) {
		$returnvar=null;
		if(util::valEmail($input)) {
			$returnvar=filter_var($input, FILTER_SANITIZE_EMAIL);
		}
		return $returnvar;
	}
	
	/*******************
	* Validates a standard username. Checks against standard
	* input string validation and for symbols
	* @param (String) $input
	* @return Boolean
	*******************/
	public static function valUName($input) {
		if(util::valStr($input) && htmlentities($input)==$input) {
			return true;
		} else {return false;}
	}

	public static function posted($input) {
		return ($input!="" && $input!=null);
	}
}

?>