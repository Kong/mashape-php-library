<?php

require_once("mashape/mashape.php");

// The path of the XML configuration file required by Mashape. By default it's "./api.xml"
define("CONFIGURATION_FILEPATH", "./api.xml");

// This is the server key for your component
define("SERVER_KEY", "the-server-key");

class ComponentAPI extends MashapeRestAPI
{
	// Don't edit the constructor code
	public function __construct() {
		parent::__construct(dirname(__FILE__));
	}

	// TODO: Declare your API functions below
	public function sayHello($name) {
		return "Hello " . $name . "!";
	}

}

// Init the library
MashapeHandler::handleApi(new ComponentAPI(), SERVER_KEY);

?>