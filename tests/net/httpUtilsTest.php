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

require_once(MASHAPE_LIBRAY_PATH . "/net/httpUtils.php");

class HttpUtilsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @TODO: This is an integration test with www.mashape.com
	 *        This test could fail if the site is unreachable (maintenance, dns, proxy, firewall etc.)
	 *        The external resource should be mocked
	 *        ~ dluc
	 */
	function testMakeHttpRequest() {
		$response = HttpUtils::makeHttpRequest("http://www.mashape.com");
		$this->assertFalse(empty($response));
	}
}
