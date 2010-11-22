<?php

require_once("mashape/mashape.php");

class YourAPI extends MashapeRestAPI
{
        // Your functions here
	public function sayHello($name) {
		return "Hello " . $name . "!";
	}

}

// Handle the requests
MashapeHandler::handleApi(new YourAPI());

?>
