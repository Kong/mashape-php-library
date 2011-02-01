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
require_once(dirname(__FILE__) . "/../../../utils/routeUtils.php");

function findRoute($requestUri, &$routeParameters, $serverKey) {
	$routeMethod = null;

	$configuration = RESTConfigurationLoader::loadConfiguration($serverKey);
	$methods = $configuration->getMethods();

	// Remove any folder before the route URL
	$scriptUrl = $_SERVER["PHP_SELF"];
	
	$fileParts = Explode('/', $scriptUrl);
	unset($fileParts[count($fileParts) - 1]);
	$requestUri = substr($requestUri, strlen(implode("/", $fileParts)));
	
	$requestUri = Explode("?", substr($requestUri, 1));
	$requestUriParts = Explode("/", $requestUri[0]);

	foreach ($methods as $method) {
		$route = $method->getRoute();
		if (!empty($route)) {
			$routeParts = Explode("/", substr($route, 1));
			if (count($requestUriParts) == count($routeParts)) {
				for ($i=0;$i<count($routeParts);$i++) {
					if ($routeParts[$i] != $requestUriParts[$i]) {
						if (RouteUtils::isRoutePlaceholder($routeParts[$i])) {
							$placeHolder = RouteUtils::getRoutePlaceholder($routeParts[$i]);
							$routeParameters[$placeHolder] = $requestUriParts[$i];
						} else {
							break;
						}
					}
					if ($i == count($routeParts) - 1) {
						$routeMethod = $method;
					}
				}
			}
		}
		if ($routeMethod != null) {
			break;
		}
	}
	
	return $routeMethod;
}

?>