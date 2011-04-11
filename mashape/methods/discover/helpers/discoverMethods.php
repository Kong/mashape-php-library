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

require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/../../../utils/routeUtils.php");

function discoverMethods($instance, $configuration, &$objectsFound, &$objectsToCreate, $scriptName) {
	// Serialize methods
	$result = "";
	$methods = $configuration->getMethods();
	foreach($methods as $method) {
		$result .= "\t<method ";
		$name = $method->getName();
		$result .= 'name="' . $name . '"';
		$http = $method->getHttp();
		$result .= " http=\"" . strtoupper($http) . "\">\n";
		$route = $method->getRoute();
		
		$result .= "\t\t<url><![CDATA[";
		if (empty($route)) {
			$result .= "/" . $scriptName . "?_method=" . $name . serializeParametersQueryString($method, $instance);
		} else {
			$result .= $route;
		}
		$result .= "]]></url>\n";
		$result .= serializeParameters($method, $instance);
		
		$object = $method->getObject();
		$resultName = $method->getResult();
		
		if (!empty($object)) {
			$result .= "\t\t<result object=\"" . $object . "\"";
			array_push($objectsFound, $object);
		}
		
		if (!empty($resultName)) {
			$uniqueName = findUniqueObjectName($objectsToCreate, $resultName, 0);
			$result .= "\t\t<result object=\"" . $uniqueName . "\"";
			$objectsToCreate[$uniqueName] = $resultName;
		}
		
		$array = $method->isArray();
		$result .= " array=\"" . ($array ? "true" : "false") . "\" />\n";

		$result .= "\t\t<error object=\"StandardMashapeError\" array=\"true\" />\n";
		
		$result .= "\t</method>\n";
	}
	return $result;
}

function findUniqueObjectName($objects, $name, $index) {
	$numeratedName = $name;
	if ($index > 0) {
		$numeratedName .= $index;
	}
	$keys = array_keys($objects);
	foreach ($keys as $key) {
		if ($key == $numeratedName) {
			return findUniqueObjectName($objects, $name, $index + 1);
		}
	}
	return $numeratedName;
}

function serializeParametersQueryString($method, $instance) {
	$reflectedClass = new ReflectionClass(get_class($instance));
	$reflectedMethod = $reflectedClass->getMethod($method->getName());
	$reflectedParameters = $reflectedMethod->getParameters();
	$result = "";
	for ($i=0;$i<count($reflectedParameters);$i++) {
		$param = $reflectedParameters[$i];
		if ($i == 0) {
			$result .= "&";
		}
		$result .= $param->name . "={" . $param->name . "}&";
	}
	
	$result = JsonUtils::removeLastChar($reflectedParameters, $result);
	return $result;
}

function serializeParameters($method, $instance) {
	$reflectedClass = new ReflectionClass(get_class($instance));
	$reflectedMethod = $reflectedClass->getMethod($method->getName());
	$reflectedParameters = $reflectedMethod->getParameters();
	$result = "\t\t<parameters>\n";
	for ($i=0;$i<count($reflectedParameters);$i++) {
		$param = $reflectedParameters[$i];
		$result .= "\t\t\t<parameter optional=\"" . ($param->isDefaultValueAvailable() ? "true" : "false") . "\">" . $param->name . "</parameter>\n";
	}
	
	$result .= "\t\t</parameters>\n";
	
	// Check route parameters
	$route = $method->getRoute();
	if (!empty($route)) {
		$routeParts = Explode("/", substr($route, 1));
		foreach ($routeParts as $routePart) {
			if (RouteUtils::isRoutePlaceholder($routePart)) {
				$placeHolder = RouteUtils::getRoutePlaceholder($routePart);
				$exist = false;
				for ($i=0;$i<count($reflectedParameters);$i++) {
					$param = $reflectedParameters[$i];
					if ($placeHolder == $param->name) {
						if ($param->isDefaultValueAvailable()) {
							throw new MashapeException(sprintf(EXCEPTION_METHOD_OPTIONAL_ROUTE_PARAM, $param->name, $method->getName()),EXCEPTION_XML_CODE);
						}
						$exist = true;
						break;
					}
				}
				if ($exist == false) {
					throw new MashapeException(sprintf(EXCEPTION_METHOD_INVALID_ROUTE_PARAM, $placeHolder),EXCEPTION_XML_CODE);
				}
			}
		}
	}

	return $result;
}
