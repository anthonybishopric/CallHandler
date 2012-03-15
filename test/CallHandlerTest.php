<?php

require_once '../src/CallHandler.php';

class A{
	use CallHandler;
	
	private $karma = 'good';
}

class B implements Receiver{
	
	public function do_call($name, $args){
		return new Receipt(strrev($name));
	}
}

class CallHandlerTest extends PHPUnit_Framework_TestCase{

	public function test_simple_name_is_added_should_support_function(){
		$a = new A();
		$a->add_match_receiver('/foo/', function($matches, $args){
			return $args[0] * $args[1];
		});
		$this->assertEquals(6, $a->foo(2,3));
	}
	
	public function test_adding_regex_value_should_be_supported(){
		$a = new A();
		$a->add_match_receiver('/add_(\d+)_to_(\d+)/', function($matches, $args){
			return $matches[1] + $matches[2];
		});
		$this->assertEquals(5, $a->add_2_to_3());
	}
	
	public function test_scope_of_this_inside_match_receiver_refers_to_the_recipient(){
		$a = new A();
		$a->add_match_receiver('/karma_detect/', function($matches, $args){
			return $this->karma;
		});
		$this->assertEquals('good', $a->karma_detect());
	}
}