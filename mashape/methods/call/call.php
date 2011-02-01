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
define("MASHAPE_TOKEN_VALIDATION_URL", "http://api.mashape.com/validateToken");

class Call implements IMethodHandler {

	public function handle($instance, $serverKey, $parameters, $httpRequestMethod) {
		// If the request comes from local, reload the configuration
		$this->reloadConfiguration($serverKey);

		$methodName = null;
		$method = null;
		
		$this->findMethod($parameters, $methodName, $method, $serverKey);
		
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

		//Validate Request
		if (self::validateRequest($serverKey, $token, $methodName, $language, $version)) {
			return doCall($method, $parameters, $instance, $serverKey);
		} else {
			throw new MashapeException(EXCEPTION_AUTH_INVALID, EXCEPTION_AUTH_INVALID_CODE);
		}
	}

	private function findMethod(&$parameters, &$methodName, &$method, $serverKey) {
		$methodName = (isset($parameters[METHOD])) ? $parameters[METHOD] : null;
		$method = null;
		if (empty($methodName)) {
			// Find route
			$requestUri = (isset($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : null;
			
			$method = findRoute($requestUri, $parameters, $serverKey);
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

	private function reloadConfiguration($serverKey) {
		if (HttpUtils::isLocal()) {
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
			$url = MASHAPE_TOKEN_VALIDATION_URL . "?" . QUERY_PARAM_TOKEN . "=" . $token . "&" . QUERY_PARAM_SERVERKEY . "=" . $serverKey . "&" . QUERY_PARAM_METHOD . "=" . $method . "&" . QUERY_PARAM_LANGUAGE . "=" . $language . "&" . QUERY_PARAM_VERSION . "=" . $version;
			$response = HttpUtils::makeHttpRequest($url);
			if (empty($response)) {
				throw new MashapeException(EXCEPTION_EMPTY_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
			}
			$validationResponse = json_decode($response);
			if (empty($validationResponse)) {
				throw new MashapeException(EXCEPTION_JSONDECODE_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
			}
			if (!empty($validationResponse->errors)) {
				$error = $validationResponse->errors[0];
				throw new MashapeException($error->message, $error->code);
			}
			$authorization = $validationResponse->authorized;
			if ($authorization == true) {
				return true;
			} else {
				return false;
			}
		}
	}

}
?>