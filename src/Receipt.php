<?php

/**
* Wrapper class for the result of an invocation on a Receiver that was successful.
*/
class Receipt{
	
	public function __construct($return_value){
		$this->return_value = $return_value;
	}
	
	public function return_value(){
		return $this->return_value;
	}
}