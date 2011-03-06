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

require_once(dirname(__FILE__) . "/../../../mashape/mashape.php");
require_once(dirname(__FILE__) . "/../../../mashape/methods/discover/discover.php");

class DiscoverTest extends PHPUnit_Framework_TestCase
{
	function testDiscover() {
		$discover = new Discover();
		try {
			$result = $discover->handle(new SampleAPI(), "serverkey", null, "post");
			$this->assertTrue(false);
		} catch (Exception $e) {
			if ($e instanceof MashapeException) {
				$this->assertTrue(true);
			} else {
				$this->assertTrue(false);
			}
		}
	}
}

class SampleAPI extends MashapeRestAPI {

	// Don't edit the constructor code
	public function __construct() {
		parent::__construct(dirname(__FILE__));
	}

	public function sayHello($name) {
		return "Hi, " . $name;
	}
}
