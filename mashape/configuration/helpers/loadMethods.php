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

require_once(dirname(__FILE__) . "/../../xml/xmlParser.php");
require_once(dirname(__FILE__) . "/../../xml/xmlParserUtils.php");
require_once(dirname(__FILE__) . "/../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../restMethod.php");

define("XML_METHOD", "method");
define("XML_METHOD_NAME", "name");
define("XML_METHOD_HTTP", "http");
define("XML_METHOD_ROUTE", "route");

define("XML_RESULT", "result");
define("XML_RESULT_ARRAY", "array");
define("XML_RESULT_TYPE", "type");
define("XML_RESULT_NAME", "name");

function loadMethodsFromXML($xmlParser) {
	$methods = array();
	
	$xmlMethods = XmlParserUtils::getChildren($xmlParser->document, XML_METHOD);
	
	foreach ($xmlMethods as $xmlMethod) {
		$name = (XmlParserUtils::existAttribute($xmlMethod, XML_METHOD_NAME)) ? $xmlMethod->tagAttrs[XML_METHOD_NAME] : null;
		
		if (empty($name)) {
			throw new MashapeException(EXCEPTION_METHOD_EMPTY_NAME, EXCEPTION_XML_CODE);
		} else if (existMethod($methods, $name)) {
			throw new MashapeException(sprintf(EXCEPTION_METHOD_DUPLICATE_NAME, $name), EXCEPTION_XML_CODE);
		}

		$http = (XmlParserUtils::existAttribute($xmlMethod, XML_METHOD_HTTP)) ? $xmlMethod->tagAttrs[XML_METHOD_HTTP] : null;
		if (empty($http)) {
			throw new MashapeException(EXCEPTION_METHOD_EMPTY_HTTP, EXCEPTION_XML_CODE);
		} else {
			$http = strtolower($http);
			if ($http != "get" && $http != "post" && $http != "put" && $http != "delete") {
				throw new MashapeException(sprintf(EXCEPTION_METHOD_INVALID_HTTP, $http),EXCEPTION_XML_CODE);
			}
		}
		
		$route = (XmlParserUtils::existAttribute($xmlMethod, XML_METHOD_ROUTE)) ? $xmlMethod->tagAttrs[XML_METHOD_ROUTE] : null;
		if (!empty($route)) {
			if (!validateRoute($route)) {
				throw new MashapeException(sprintf(EXCEPTION_METHOD_INVALID_ROUTE, $route),EXCEPTION_XML_CODE);
			} else {
				if (existRoute($methods, $route)) {
					throw new MashapeException(sprintf(EXCEPTION_METHOD_DUPLICATE_ROUTE, $route), EXCEPTION_XML_CODE);
				}
			}
		}

		// Get the result
		$resultsNode = $xmlMethod->result;
		
		$resultNode = null;
		if (count($resultsNode) > 1) {
			throw new MashapeException(sprintf(EXCEPTION_RESULT_MULTIPLE, $name), EXCEPTION_XML_CODE);
		} elseif (count($resultsNode)==1) {
			$resultNode = $resultsNode[0];
		} else {
			throw new MashapeException(sprintf(EXCEPTION_RESULT_MISSING, $name), EXCEPTION_XML_CODE);
		}
		
		$object = null;
		$array = null;
		$resultName = null;

		if ($resultNode != null) {
			
			$array = (XmlParserUtils::existAttribute($resultNode, XML_RESULT_ARRAY)) ? $resultNode->tagAttrs[XML_RESULT_ARRAY] : null;
			if ($array != null && strtolower($array) == "true") {
				$array = true;
			} else {
				$array = false;
			}

			$type = (XmlParserUtils::existAttribute($resultNode, XML_RESULT_TYPE)) ? $resultNode->tagAttrs[XML_RESULT_TYPE] : null;
			if (strtolower($type=="simple")) {
				$resultName = (XmlParserUtils::existAttribute($resultNode, XML_RESULT_NAME)) ? $resultNode->tagAttrs[XML_RESULT_NAME] : null;
				if (empty($resultName)) {
					throw new MashapeException(sprintf(EXCEPTION_RESULT_EMPTY_NAME_SIMPLE, $name), EXCEPTION_XML_CODE);
				}
			} else if (strtolower($type=="complex")) {
				$object = (XmlParserUtils::existAttribute($resultNode, XML_RESULT_NAME)) ? $resultNode->tagAttrs[XML_RESULT_NAME] : null;
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
		$method->setRoute($route);

		//Save method
		array_push($methods, $method);

	}
	return $methods;
}

function validateRoute($route) {
	if (preg_match("/^(\/\{\w+\})*\/\w+(\/\w+)*(\/\{\w+\})*(\/\w+)*(\/\{\w+\})*$/", $route)) {
		return true;
	} else {
		return false;
	}
}

function existRoute($methods, $route) {
	foreach ($methods as $method) {
		if ($method->getRoute() == $route) {
			return true;
		}
	}
	return false;
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