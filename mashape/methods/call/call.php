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
define("TOKEN", "_token");
define("ROUTE", "_route");
define("LANGUAGE", "_language");
define("VERSION", "_version");
define("QUERY_PARAM_TOKEN", "token");
define("QUERY_PARAM_METHOD", "method");
define("QUERY_PARAM_SERVERKEY", "serverkey");
define("QUERY_PARAM_LANGUAGE", "language");
define("QUERY_PARAM_VERSION", "version");
define("MASHAPE_TOKEN_VALIDATION_URL", "https://api.mashape.com/validateToken");

class Call implements IMethodHandler {

	public function handle($instance, $serverKey, $parameters, $httpRequestMethod) {
		// If the request comes from local, reload the configuration
		$this->reloadConfiguration($instance, $serverKey);

		$methodName = null;
		$method = null;
		
		$this->findMethod($parameters, $methodName, $method, $serverKey, $httpRequestMethod);
		
		if (strtolower($method->getHttp()) != strtolower($httpRequestMethod)) {
			throw new MashapeException(EXCEPTION_INVALID_HTTPMETHOD, EXCEPTION_INVALID_HTTPMETHOD_CODE);
		}

		unset($parameters[METHOD]); // Remove the method name from the params
		$token = (isset($parameters[TOKEN])) ? $parameters[TOKEN] : null;
		unset($parameters[TOKEN]); // remove the token parameter

		$language = (isset($parameters[LANGUAGE])) ? $parameters[LANGUAGE] : null;
		unset($parameters[LANGUAGE]); // remove the language parameter
		$version = (isset($parameters[VERSION])) ? $parameters[VERSION] : null;
		unset($parameters[VERSION]); // remove the version parameter

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

	private function validateRequest($serverKey, $token, $method, $language, $version) {
		// If the request comes from the local computer, then don't require authorization,
		// otherwise check the headers
		if (HttpUtils::isLocal()) {
			return true;
		} else {
			if (empty($serverKey)) {
				throw new MashapeException(EXCEPTION_EMPTY_SERVERKEY, EXCEPTION_XML_CODE);
			}
			$url = MASHAPE_TOKEN_VALIDATION_URL . "?" . QUERY_PARAM_TOKEN . "=" . urlencode($token) . "&" . QUERY_PARAM_SERVERKEY . "=" . urlencode($serverKey) . "&" . QUERY_PARAM_METHOD . "=" . urlencode($method) . "&" . QUERY_PARAM_LANGUAGE . "=" .urlencode($language) . "&" . QUERY_PARAM_VERSION . "=" . urlencode($version);
			$response = HttpUtils::makeHttpRequest($url);
			if (empty($response)) {
				throw new MashapeException(EXCEPTION_EMPTY_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
			}
			$validationResponse = json_decode($response);
			if (empty($validationResponse)) {
				throw new MashapeException(EXCEPTION_JSONDECODE_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
			}
			if (!empty($validationResponse->error)) {
				$error = $validationResponse->error;
				throw new MashapeException($error->message, $error->code);
			}
			$authorization = $validationResponse->authorized;
			$GLOBALS[UID] = $validationResponse->uid;
			if ($authorization == true) {
				return true;
			} else {
				return false;
			}
		}
	}
}
