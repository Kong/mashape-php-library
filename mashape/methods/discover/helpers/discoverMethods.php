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

function discoverMethods($instance, $configuration, &$objectsFound) {
	// Serialize methods
	$result = '"methods":[';
	$methods = $configuration->getMethods();
	foreach($methods as $method) {
		$result .= "{";
		$name = $method->getName();
		$result .= '"name":"' . $name . '",';
		$object = $method->getObject();
		$route = $method->getRoute();
		if (empty($object)) {
			$result .= '"object":null,';
		} else {
			$result .= '"object":"' . $object . '",';
			array_push($objectsFound, $object);
		}
		if (empty($route)) {
			$result .= '"route":null,';
		} else {
			$result .= '"route":"' . $route . '",';
		}
		$resultName = $method->getResult();
		if (empty($resultName)) {
			$result .= '"result":null,';
		} else {
			$result .= '"result":"' . $resultName . '",';
		}
		$array = $method->isArray();
		$result .= '"array":' . ($array ? "true" : "false") . ',';
		$http = $method->getHttp();
		$result .= '"http":"' . $http . '",';
		$result .= serializeParameters($method, $instance);

		$result .= "},";
	}
	// Remove the last comma
	$result = JsonUtils::removeLastChar($methods, $result);
	$result .= "]";
	return $result;
}

function serializeParameters($method, $instance) {
	$reflectedClass = new ReflectionClass(get_class($instance));
	$reflectedMethod = $reflectedClass->getMethod($method->getName());
	$reflectedParameters = $reflectedMethod->getParameters();
	$result = '"parameters":[';
	for ($i=0;$i<count($reflectedParameters);$i++) {
		$param = $reflectedParameters[$i];
		$result .= '{"name":"' . $param->name . '", "optional":' . ($param->isDefaultValueAvailable() ? "true" : "false") . ',"index":' . $i . '},';
	}
	// Remove the last comma
	$result = JsonUtils::removeLastChar($reflectedParameters, $result);
	
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

	$result .= ']';
	return $result;
}

?>