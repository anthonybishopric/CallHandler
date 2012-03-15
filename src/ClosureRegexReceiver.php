<?php

class ClosureRegexReceiver implements Receiver{
	
	private $pattern;
	private $closure;
	
	public function __construct($pattern, $closure){
		$this->pattern = $pattern;
		$this->closure = $closure;
	}
	
	protected function regex(){
		return $this->pattern;
	}
	
	protected function match_call($matches, $args){
		$cl = $this->closure;
		return $cl($matches, $args);
	}
	
	protected function get_matches($name){
		$matches = [];
		preg_match($this->regex(), $name, $matches);
		return $matches;
	}
	
	public function do_call($name, $args){
		$matches = $this->get_matches($name);
		if($matches){
			$value = $this->match_call($matches, $args);
			return new Receipt($value);
		}
		else{
			return null;
		}
	}
	
}