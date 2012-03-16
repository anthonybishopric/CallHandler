<?php

require_once '../CallHandler.php';

class A{
	use CallHandler;
	
	private $karma = 'good';
}

class B implements CallHandler_Receiver{
	
	public function do_call($name, $args){
		return new Receipt(strrev($name));
	}
}

class C extends A{

	public function method_missing($name, $args){
		return "method_missing called";
	}
	
	public function alternate_method_missing($name, $args){
		return "alternate called";
	}
}

class CallHandlerTest extends PHPUnit_Framework_TestCase{

	public function test_simple_name_is_added_should_support_function(){
		$a = new A();
		$a->support_methods('/foo/', function($matches, $args){
			return $args[0] * $args[1];
		});
		$this->assertEquals(6, $a->foo(2,3), "args did not propagate properly to the dynamic function");
	}
	
	public function test_adding_regex_match_values_should_be_supported(){
		$a = new A();
		$a->support_methods('/add_(\d+)_to_(\d+)/', function($matches, $args){
			return $matches[1] + $matches[2];
		});
		$this->assertEquals(5, $a->add_2_to_3(), "regex values did not propagate properly to the dynamic function");
	}
	
	public function test_scope_of_this_inside_match_receiver_refers_to_the_caller(){
		$a = new A();
		$this->karma = "bad";
		$a->support_methods('/karma_detect/', function($matches, $args){
			return $this->karma;
		});
		$this->assertEquals('bad', $a->karma_detect(), "should have used this test's \$this instead of \$a's \$this");
	}
	
	public function test_null_value_should_be_returned_if_matched_function_returns_null(){
		$a = new A();
		$a->support_methods('/foo/', function(){
			return null;
		});
		$this->assertNull($a->foo(), "null should have been matched");
	}
	
	public function test_multiple_receivers_should_all_be_queried_for_matches(){
		// support arbitrary setters on this object
		$a = new A();
		$a->support_methods('/set_(.+)/', function($matches, $args) use ($a){

			// $matches is just the result of preg_match.
			
			$field = $matches[1];
			$a->$field = $args[0];
		});

		// support arbitrary getters on this object
		$a->support_methods('/get_(.+)/', function($matches, $args) use ($a){
			$field = $matches[1];
			return $a->$field;
		});
		
		$a->set_first_name("Anthony");
		$this->assertEquals("Anthony", $a->get_first_name());
	}
	
	/**
	* @expectedException CallHandler_UnsupportedCallException
	*/
	public function test_failure_to_match_any_receivers_should_throw_an_exception(){
		$a = new A();
		$a->any_method();
	}
	
	public function test_if_no_matcher_is_found_will_try_to_use_method_missing_if_it_exists(){
		$c = new C();
		$this->assertEquals('method_missing called', $c->any_method(), "expected method missing to be called");
	}
	
	public function test_if_alternate_call_fallback_is_set_will_try_to_use_that_instead_of_method_missing(){
		$c = new C();
		$c->set_call_fallback('alternate_method_missing');
		$this->assertEquals('alternate called', $c->any_method(), "expected alternate method missing to be called");
	}
}