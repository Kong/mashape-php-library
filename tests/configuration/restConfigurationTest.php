<?php

/**
 * Test RESTConfiguration class
 *
 * @author Devis Lucato <devis@lucato.it>
 */

require_once(MASHAPE_LIBRAY_PATH . "/configuration/restConfiguration.php");

class RESTConfigurationTest extends PHPUnit_Framework_TestCase
{
	protected $_instance;

	public function setUp()
	{
		$this->_instance = new RESTConfiguration();
	}

	/**
	 * @covers RESTConfiguration::setMethods
	 * @covers RESTConfiguration::getMethods
	 */
	public function test_setMethods_and_getMethods()
	{
		$value = __METHOD__ . time();
		$this->_instance->setMethods($value);
		$this->assertEquals($value, $this->_instance->getMethods());
	}

	/**
	 * @covers RESTConfiguration::setObjects
	 * @covers RESTConfiguration::getObjects
	 */
	public function test_setObjects_and_getObjects()
	{
		$value = __METHOD__ . time();
		$this->_instance->setObjects($value);
		$this->assertEquals($value, $this->_instance->getObjects());
	}
}