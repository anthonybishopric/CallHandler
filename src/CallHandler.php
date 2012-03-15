<?php

require_once 'Receiver.php';
require_once 'Receipt.php';
require_once 'ClosureRegexReceiver.php';

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
	
	private $receivers = null;
	
	public function __construct(){
		$this->receivers = [];
	}
	
	public function add_match_receiver($pattern, $closure){
		$this->add_call_receiver(new ClosureRegexReceiver($pattern, $closure->bindTo($this, $this)));
	}
	
	public function add_call_receiver(Receiver $receiver){
		$this->receivers[] = $receiver;
	}
	
	public function __call($name, $args){
		foreach($this->receivers as $receiver){
			$receipt = $receiver->do_call($name, $args);
			if($receipt){
				return $receipt->return_value();
			}
		}
	}
}