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

require_once(dirname(__FILE__) . "/../IMethodHandler.php");
require_once(dirname(__FILE__) . "/../../net/httpUtils.php");
require_once(dirname(__FILE__) . "/../../init/init.php");
require_once(dirname(__FILE__) . "/../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/helpers/callHelper.php");
require_once(dirname(__FILE__) . "/helpers/routeHelper.php");
require_once(dirname(__FILE__) . "/../discover/helpers/updateHtaccess.php");

define("METHOD", "_method");

class Call implements IMethodHandler {

	public function handle($instance, $serverKey, $parameters, $httpRequestMethod) {
		// If the request comes from local, reload the configuration
		$this->reloadConfiguration($instance, $serverKey);

		$methodName = null;
		$method = null;
		
		$this->findMethod($parameters, $methodName, $method, $serverKey, $httpRequestMethod);
		
		$instance::$xmlRoot = $methodName;
		
		if (strtolower($method->getHttp()) != strtolower($httpRequestMethod)) {
			throw new MashapeException(EXCEPTION_INVALID_HTTPMETHOD, EXCEPTION_INVALID_HTTPMETHOD_CODE);
		}

		unset($parameters[METHOD]); // Remove the method name from the params

		return doCall($method, $parameters, $instance, $serverKey);
	}

	private function findMethod(&$parameters, &$methodName, &$method, $serverKey, $httpRequestMethod) {
		$methodName = (isset($parameters[METHOD])) ? $parameters[METHOD] : null;
		$method = null;
		if (empty($methodName)) {
			// Find route
			$requestUri = (isset($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : null;
			
			$method = findRoute($requestUri, $parameters, $httpRequestMethod, $serverKey);
			if (!empty($method)) {
				$methodName = $method->getName();
			}
		} else {
			$method = RESTConfigurationLoader::getMethod($methodName, $serverKey);
		}
		if (empty($method)) {
			throw new MashapeException(sprintf(EXCEPTION_METHOD_NOTFOUND, $methodName), EXCEPTION_METHOD_NOTFOUND_CODE);
		}
	}

	private function reloadConfiguration($instance, $serverKey) {
		if (HttpUtils::isLocal()) {
			// Update the .htaccess file with the new route settings
			updateHtaccess($instance);
			
			// Update the configuration
			RESTConfigurationLoader::reloadConfiguration($serverKey);
		}
	}

}
