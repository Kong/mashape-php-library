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

require_once(MASHAPE_LIBRAY_PATH . "/json/jsonUtils.php");

class JsonUtilsTest extends PHPUnit_Framework_TestCase
{
	function testSerializeError() {
		$this->assertEquals('{"errors":[{"message":"this is an error","code":2}], "result":null}', JsonUtils::serializeError("this is an error", 2));
		$this->assertEquals('{"errors":[{"message":"this is an error","code":null}], "result":null}', JsonUtils::serializeError("this is an error", null));
		$this->assertEquals('{"errors":[{"message":null,"code":null}], "result":null}', JsonUtils::serializeError(null, null));
		$this->assertEquals('{"errors":[{"message":"this is a \"great\" error","code":2}], "result":null}', JsonUtils::serializeError('this is a "great" error', 2));
	}
}
