Better magic methods in PHP
===========================
*CallHandler* is a simple trait you can include in PHP 5.4 objects that lets you easily add dynamic methods to your objects. Simply pass a regular expression that should be caught (with any groups you'd like to match against) and a closure with the two arguments ($matches, $args)

Example use
-----------
``` PHP
require_once 'CallHandler.php';

class User{
	use CallHandler;
	
	public function __construct(){
		
		// support arbitrary setters on this object
		$this->add_match_receiver('/set_(.+)/', function($matches, $args){

			// $matches is just the result of preg_match.
			
			$field = $matches[1];
			$this->$field = $args[0];
		});

		// support arbitrary getters on this object
		$this->add_match_receiver('/get_(.+)/', function($matches, $args){
			$field = $matches[1];
			return $this->$field;
		});
	}
	
	public function method_missing($name, $args){
		return "hi $name!";
	}
	
	public function alternate_call_handler($name, $args){
		return "$name was called through an alternate call handler function";
	}
}

$user = new User();
$user->set_first_name("Anthony");
echo "hi ". $user->get_first_name(); // "hi Anthony"

// CallHandler will override your __call method in order to work. If you'd like your
// own __call-like method to continue to work, CallHandler will look for #method_missing in your
// object or you can specify an alternate fallback if #method_missing grosses you out.

$user->francis(); // "hi francis!"

$user->set_call_fallback("alternate_call_handler");

$user->francis(); // "francis was called through an alternate call handler function"
```	
Questions?
----------
Feel free to message me on here. 