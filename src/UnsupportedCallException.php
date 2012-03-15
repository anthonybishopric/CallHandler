<?php

class UnsupportedCallException extends Exception{
	
	public function __construct($name){
		parent::__construct("$name is not supported");
	}
}