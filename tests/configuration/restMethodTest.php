<?php

/**
 * Test RESTMethod class
 *
 * @author Devis Lucato <devis@lucato.it>
 */

require_once(MASHAPE_LIBRAY_PATH . "/configuration/restMethod.php");

class RESTMethodTest extends PHPUnit_Framework_TestCase
{
	protected $_instance;

	public function setUp()
	{
		$this->_instance = new RESTMethod();
	}

	/**
	 * @covers RESTMethod::setResult
	 * @covers RESTMethod::getResult
	 */
	public function test_setResult_and_getResult()
	{
		$value = __METHOD__ . time();
		$this->_instance->setResult($value);
		$this->assertEquals($value, $this->_instance->getResult());
	}

	/**
	 * @covers RESTMethod::setObject
	 * @covers RESTMethod::getObject
	 */
	public function test_setObject_and_getObject()
	{
		$value = __METHOD__ . time();
		$this->_instance->setObject($value);
		$this->assertEquals($value, $this->_instance->getObject());
	}

	/**
	 * @covers RESTMethod::setName
	 * @covers RESTMethod::getName
	 */
	public function test_setName_and_getName()
	{
		$value = __METHOD__ . time();
		$this->_instance->setName($value);
		$this->assertEquals($value, $this->_instance->getName());
	}

	/**
	 * @covers RESTMethod::setHttp
	 * @covers RESTMethod::getHttp
	 */
	public function test_setHttp_and_getHttp()
	{
		$value = __METHOD__ . time();
		$this->_instance->setHttp($value);
		$this->assertEquals($value, $this->_instance->getHttp());
	}

	/**
	 * @covers RESTMethod::setRoute
	 * @covers RESTMethod::getRoute
	 */
	public function test_setRoute_and_getRoute()
	{
		$value = __METHOD__ . time();
		$this->_instance->setRoute($value);
		$this->assertEquals($value, $this->_instance->getRoute());
	}

	/**
	 * @covers RESTMethod::setArray
	 * @covers RESTMethod::isArray
	 */
	public function test_setArray_and_isArray()
	{
		$this->markTestSkipped('isArray() implementation does not verify if the value is an array');
		$value = __METHOD__ . time();
		$this->_instance->setArray($value);
		$this->assertEquals($value, $this->_instance->isArray());
	}
}