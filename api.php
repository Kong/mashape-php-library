<?php

require_once("mashape/mashape.php");

class Ret {
	public $name;
	public $age;
	public $nick;
}

class User {
	public $name;
	public $email;
	public $ret;
}

class Tag {
	public $text;
	public $author;
}

class Second {
	public $tag;
	public $rating;
	public $user;
}

class HelloWorldAPI extends MashapeRestAPI
{
	public function sayHello($name, $nick = "default nickname") {
		$ret = new Ret();
		$ret->name = $name;
		$ret->age = array(3, 4, 10);
		$ret->nick = $nick;
		return $ret;
	}
	
	public function complex1($param1, $param2, $param3) {
		return new User();
	}
	
	public function complex2($param1) {
		return new Second();
	}
	
	public function touch() {
		return "Ouch!";
	}
}

// Handle the requests
MashapeHandler::handleAPI(new HelloWorldAPI());

?>

