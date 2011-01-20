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

require_once(dirname(__FILE__) . "/../../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/serializeObject.php");

function serializeMethodResult($method, $result, $instance) {
	$json = "";

	$isSimpleResult = isSimpleResult($method);

	if ($result == null) {
		if($isSimpleResult) {
			$json .= '{"' . $method->getResult() . '":null}';
		} else {
			$json .= "null";
		}
	} else {
		if ($isSimpleResult) {
			$json .= '{"' . $method->getResult() . '":';
		}
		if ($method->isArray()) {
			$json .= "[";
			if (is_array($result)) {
				for ($i=0;$i<count($result);$i++) {
					$json .=  serializeObject($result[$i], $instance, $isSimpleResult) . ",";
				}
				$json = JsonUtils::removeLastChar($result, $json);
			} else {
				// The result it's not an array although it was described IT WAS an array
				throw new MashapeException(EXCEPTION_EXPECTED_ARRAY_RESULT, EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
			}
			$json .= "]";
		} else {
			if (is_array($result)) {
				// The result it's an array although it was described IT WAS NOT an array
				throw new MashapeException(EXCEPTION_UNEXPECTED_ARRAY_RESULT, EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
			} else {
				$json .= serializeObject($result, $instance, $isSimpleResult);
			}
		}
		if ($isSimpleResult) {
			$json .= '}';
		}
	}
	return $json;
}

function isSimpleResult($method) {
	$resultName = $method->getResult();
	$objectName = $method->getObject();
	if (empty($resultName) && !empty($objectName)) {
		return false;
	}
	return true;
}

?>