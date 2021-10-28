<?php
class DOB extends DateTime {
	
	/*******************
	* Accepts a date string, or DateTime object and sets the 
	* date of the current instance of DOB to that date if it
	* is valid
	* @param Mixed String/Datetime $date 
	* @param String $format Option - Defaults to Y-m-d, eg 1990-01-01
	* @return Boolean Success flag
	********************/
	public function setDOB($date, $format="Y-m-d") {
		$success=true;
		if(!($date instanceof DateTime)) {
			if($this->validDate($date, $format)) {
				$d = DateTime::createFromFormat($format, $date);
			} else {
				$success=false;
			}
		} else {$d=$date;}
		$this->setDate($d->format("Y"),$d->format("m"),$d->format("d"));
		return $success;
	}
	
	/*********************
	* Validates a date String or DateTime object
	* @param $date Mixed String/DateTime - date to test
	* @param String $format Optional format string
	* @return Boolean - Returns true if date is valid, false if not
	**********************/
	public function validDate($date, $format="Y-m-d") {
		$valid=false;
		if(!($date instanceof DateTime)) {
			$d = DateTime::createFromFormat($format, $date);
			if($d && $d->format($format) == $date) { $valid=true; }
		} else {
			$valid=true;
		}
		return $valid;
	}
	
	/*******************
	* Extends capability of diff method of DateTime to compare
	* DateTime and String input dates. Returns the difference between
	* the date parameter and the date stored in the current instance
	* of DOB. Returns false if the date parameter is invalid.
	* @param $date Mixed String/DateTime - date to check difference
	* @param $format String Optional formate, defaults to Y-m-d
	* @return Mixed DateInterval object or false.
	* See http://php.net/manual/en/datetime.diff.php for more
	*******************/
	public function difference($date, $format="Y-m-d") {
		if(!($date instanceof DateTime)) {
			$d=DateTime::createFromFormat($format, $date);
		} else {$d=$date;}
		if($this->validDate($d,$format)) {
			return $this->diff($d);
		} else {
			throw new Exception("Invalid Comparison Date");
		}
	}
	
	/*********************
	* Calculates the age in years based upon the date stored
	* in the current instance of the DOB class.
	* @return Integer - Age in years
	*********************/
	public function getAge() {
		$now= new DateTime();
		$diff=$this->diff($now);
		return (int)$diff->format("%y");
	}
}
?>