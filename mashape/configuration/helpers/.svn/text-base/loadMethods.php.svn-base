<?php

/*
 * Mashape PHP library.
 *
 * Copyright (C) 2010 Mashape, Inc.
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

require_once(dirname(__FILE__) . "/../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../restMethod.php");

define("XML_METHOD", "method");
define("XML_METHOD_NAME", "name");
define("XML_METHOD_HTTP", "http");

define("XML_RESULT", "result");
define("XML_RESULT_ARRAY", "array");
define("XML_RESULT_TYPE", "type");
define("XML_RESULT_NAME", "name");

function loadMethodsFromXML($xmlDoc) {
	$methods = array();

	$xmlMethods= $xmlDoc->getElementsByTagName(XML_METHOD);
	foreach ($xmlMethods as $xmlMethod) {
		$name = $xmlMethod->getAttribute(XML_METHOD_NAME);
		if (empty($name)) {
			throw new MashapeException(EXCEPTION_METHOD_EMPTY_NAME, EXCEPTION_XML_CODE);
		} else if (existMethod($methods, $name)) {
			throw new MashapeException(sprintf(EXCEPTION_METHOD_DUPLICATE_NAME, $name), EXCEPTION_XML_CODE);
		}

		$http = $xmlMethod->getAttribute(XML_METHOD_HTTP);
		if (empty($http)) {
			throw new MashapeException(EXCEPTION_METHOD_EMPTY_HTTP, EXCEPTION_XML_CODE);
		} else {
			$http = strtolower($http);
			if ($http != "get" && $http != "post" && $http != "put" && $http != "delete") {
				throw new MashapeException(sprintf(EXCEPTION_METHOD_INVALID_HTTP, $http),EXCEPTION_XML_CODE);
			}
		}

		// Get the result
		$resultsNode = $xmlMethod->getElementsByTagName(XML_RESULT);
		$resultNode = null;
		if ($resultsNode->length > 1) {
			throw new MashapeException(sprintf(EXCEPTION_RESULT_MULTIPLE, $name), EXCEPTION_XML_CODE);
		} elseif ($resultsNode->length==1) {
			$resultNode = $resultsNode->item(0);
		} else {
			throw new MashapeException(sprintf(EXCEPTION_RESULT_MISSING, $name), EXCEPTION_XML_CODE);
		}

		$object = null;
		$array = null;
		$resultName = null;

		if ($resultNode != null) {
			$array = $resultNode->getAttribute(XML_RESULT_ARRAY);
			if ($array != null && strtolower($array) == "true") {
				$array = true;
			} else {
				$array = false;
			}

			$type = $resultNode->getAttribute(XML_RESULT_TYPE);
			if (strtolower($type=="simple")) {
				$resultName = $resultNode->getAttribute(XML_RESULT_NAME);
				if (empty($resultName)) {
					throw new MashapeException(sprintf(EXCEPTION_RESULT_EMPTY_NAME_SIMPLE, $name), EXCEPTION_XML_CODE);
				}
			} else if (strtolower($type=="object")) {
				$object = $resultNode->getAttribute(XML_RESULT_NAME);
				if (empty($object)) {
					throw new MashapeException(sprintf(EXCEPTION_RESULT_EMPTY_NAME_OBJECT, $name), EXCEPTION_XML_CODE);
				}
			} else if (empty($type)) {
				throw new MashapeException(sprintf(EXCEPTION_RESULT_EMPTY_TYPE, $name), EXCEPTION_XML_CODE);
			} else {
				throw new MashapeException(sprintf(EXCEPTION_RESULT_INVALID_TYPE, $type, $name), EXCEPTION_XML_CODE);
			}
		}

		$method = new RESTMethod();
		$method->setName($name);
		$method->setObject($object);
		$method->setResult($resultName);
		$method->setArray($array);
		$method->setHttp($http);

		//Save method
		array_push($methods, $method);

	}
	return $methods;
}

function existMethod($methods, $name) {
	foreach ($methods as $method) {
		if ($method->getName() == $name) {
			return true;
		}
	}
	return false;
}

?>