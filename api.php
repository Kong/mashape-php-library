<?php

require_once("mashape/mashape.php");

class YourAPI extends MashapeRestAPI
{
    // Your functions here
	public function sayHello($name) {
		return "Hello " . $name . "!";
	}
}

// Init the library
MashapeHandler::handleApi(new YourAPI(), "the-server-key");

?>
