<?php

/*
 * Mashape PHP library.
 *
 * Copyright (C) 2011 Mashape, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * The author of this software is Mashape, Inc.
 * For any question or feedback please contact us at: support@mashape.com
 *
 */

define("CONFIGURATION_FILEPATH", "./api.xml");
require_once(dirname(__FILE__) . "/../../mashape/configuration/restConfigurationLoader.php");

class RESTConfigurationLoaderTest extends PHPUnit_Framework_TestCase
{
	function testLoadConfiguration() {
		$serverKey = "the-server-key";
		$configuration = RESTConfigurationLoader::loadConfiguration($serverKey);
		$this->assertFalse($configuration == null);
		$this->assertEquals(1, count($configuration->getMethods()));
		$this->assertEquals(0, count($configuration->getObjects()));
	}
}
 
?>