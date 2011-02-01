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

require_once(dirname(__FILE__) . "/../../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../../../utils/routeUtils.php");
require_once(dirname(__FILE__) . "/../../../utils/arrayUtils.php");

function findRoute($requestUri, &$routeParameters, $serverKey) {
	$routeMethod = null;

	$configuration = RESTConfigurationLoader::loadConfiguration($serverKey);
	$methods = $configuration->getMethods();

	$requestUri = Explode("?", substr($requestUri, 1));
	$requestUriParts = Explode("/", $requestUri[0]);

	$likelyMethods = array();
	$excludedMethods = array();
	// Backward loop
	for ($i=count($requestUriParts) - 1; $i>=0;$i--) {
		// Find if there's a path like that, otherwise check for placeholders
		foreach ($methods as $method) {
			$methodName = $method->getName();
			if (in_array($methodName, $excludedMethods)) {
				continue;
			}
//			echo "Method " . $methodName . "\n\n";
			$route = $method->getRoute();
			if (!empty($route)) {
				$routeParts = Explode("/", substr($route, 1));
				$backwardIndex = count($routeParts) - (count($requestUriParts) - $i);
				if ($backwardIndex >= 0) {
//					echo "* RoutePart: " . $routeParts[$backwardIndex] . "\n";
//					echo "* RequestPart: " .  $requestUriParts[$i] . "\n\n";
					if ($routeParts[$backwardIndex] == $requestUriParts[$i]) {
						if (!ArrayUtils::existKey($methodName, $likelyMethods)) {
							$likelyMethods[$methodName] = array();
						}
					} else if (RouteUtils::isRoutePlaceholder($routeParts[$backwardIndex])) {
						$foundParameters;
						$placeHolder = RouteUtils::getRoutePlaceholder($routeParts[$backwardIndex]);
						if (!ArrayUtils::existKey($methodName, $likelyMethods)) {
							$foundParameters = array();
						} else {
							$foundParameters = $likelyMethods[$methodName];
						}
						$foundParameters[$placeHolder] = $requestUriParts[$i];
						$likelyMethods[$methodName] = $foundParameters;
					} else {
						array_push($excludedMethods, $methodName);
						unset($likelyMethods[$methodName]);
					}
				}
			}
		}
	}

	if (count($likelyMethods) > 1) {
		$ambiguousMethods = "";
		foreach ($likelyMethods as $key => $value) {
			$ambiguousMethods .= "\"" . $key . "\", ";
		}
		$ambiguousMethods = substr($ambiguousMethods, 0, strlen($ambiguousMethods) - 2);
		throw new MashapeException(sprintf(EXCEPTION_AMBIGUOUS_ROUTE, $ambiguousMethods), EXCEPTION_SYSTEM_ERROR_CODE);
	}

	
	// Get the first item (just one or none item can exist)
	foreach ($likelyMethods as $key => $value) {
		$routeMethod = RESTConfigurationLoader::getMethod($key, $serverKey);
		$routeParameters = array_merge($routeParameters, $value);
		break;
	}

	return $routeMethod;
}

?>