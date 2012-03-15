<?php

require_once 'Receiver.php';
require_once 'Receipt.php';
require_once 'ClosureRegexReceiver.php';
require_once 'UnsupportedCallException.php';

/**
* Better structured __call inheritance.
*
* class Foo{
*	use CallHandler
* }
*
* $foo = new Foo();
* $foo->add_match_receiver('is_(.+)_awesome', function($matches, $args){
*	return "Yes, ". $matches[1] . " is very awesome indeed."
* });
* 
* echo $foo->is_bacon_awesome(); // Yes, bacon is very awesome indeed
*
*/
trait CallHandler{
	
	private $call_handler_receivers = [];
	private $call_handler_fallback = 'method_missing';

	public function set_call_fallback($fallback){
		$this->call_handler_fallback = $fallback;
	}

	public function add_match_receiver($pattern, $closure){
		$this->add_receiver(new ClosureRegexReceiver($pattern, $closure));
	}
	
	public function add_receiver(Receiver $receiver){
		$this->call_handler_receivers[] = $receiver;
	}
	
	public function __call($name, $args){
		foreach($this->call_handler_receivers as $receiver){
			$receipt = $receiver->do_call($name, $args);
			if($receipt){
				return $receipt->return_value();
			}
		}
		if(method_exists($this, $this->call_handler_fallback)){
			$fallback = $this->call_handler_fallback;
			return $this->$fallback($name, $args);
		}
		else{
			throw new UnsupportedCallException($name);
		}
	}
}