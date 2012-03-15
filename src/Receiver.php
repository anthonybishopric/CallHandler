<?php

interface Receiver{

	/**
	* @param $name the function name being called
	* @param $args the function arguments passed
	* @return the Receipt of the Receiver's simulated __call. If null, then
	* the the function $name was not supported by the Receiver
	*/
	public function do_call($name, $args);
}