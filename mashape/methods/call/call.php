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

require_once(dirname(__FILE__) . "/../iMethodHandler.php");
require_once(dirname(__FILE__) . "/../../net/httpUtils.php");
require_once(dirname(__FILE__) . "/../../init/init.php");
require_once(dirname(__FILE__) . "/../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/helpers/callHelper.php");

define("METHOD", "_method");
define("TOKEN", "_token");
define("QUERY_PARAM_TOKEN", "token");
define("QUERY_PARAM_METHOD", "method");
define("QUERY_PARAM_SERVERKEY", "serverkey");
define("MASHAPE_TOKEN_VALIDATION_URL", "http://api.mashape.com/validateToken");

class Call implements iMethodHandler {

	public function handle($instance, $parameters, $httpRequestMethod) {
		// If the request comes from local, reload the configuration
		$this->reloadConfiguration();

		$methodName = (isset($parameters[METHOD])) ? $parameters[METHOD] : null;
		$method = RESTConfigurationLoader::getMethod($methodName);
		if (empty($method)) {
			throw new MashapeException(EXCEPTION_METHOD_NOTFOUND, EXCEPTION_METHOD_NOTFOUND_CODE);
		}
		if (strtolower($method->getHttp()) != strtolower($httpRequestMethod)) {
			throw new MashapeException(EXCEPTION_INVALID_HTTPMETHOD, EXCEPTION_INVALID_HTTPMETHOD_CODE);
		}

		unset($parameters[METHOD]); // Remove the method name from the params
		$token = (isset($parameters[TOKEN])) ? $parameters[TOKEN] : null;
		unset($parameters[TOKEN]); // remove the token parameter

		//Validate Request
		if (self::validateRequest($token, $methodName)) {
			return doCall($method, $parameters, $instance);
		} else {
			throw new MashapeException(EXCEPTION_AUTH_INVALID, EXCEPTION_AUTH_INVALID_CODE);
		}
	}

	private function reloadConfiguration() {
		if (HttpUtils::isLocal()) {
			RESTConfigurationLoader::reloadConfiguration();
		}
	}

	private function validateRequest($token, $method) {
		$serverkey = RESTConfigurationLoader::loadConfiguration()->getServerkey();
		// If the request comes from the local computer, then don't require authorization,
		// otherwise check the headers
		if (HttpUtils::isLocal()) {
			return true;
		} else {
			if (empty($serverkey)) {
				throw new MashapeException(EXCEPTION_EMPTY_SERVERKEY, EXCEPTION_XML_CODE);
			}
			$url = MASHAPE_TOKEN_VALIDATION_URL . "?" . QUERY_PARAM_TOKEN . "=" . $token . "&" . QUERY_PARAM_SERVERKEY . "=" . $serverkey . "&" . QUERY_PARAM_METHOD . "=" . $method;
			$response = HttpUtils::makeHttpRequest($url);
			$validationResponse = json_decode($response);
			if (!empty($validationResponse->error)) {
				$error = $validationResponse->error;
				throw new MashapeException($error->message, $error->code);
			}
			$authorization = $validationResponse->authorized;
			if ($authorization == true) {
				return true;
			} else {
				header("HTTP/1.0 401 Unauthorized");
				header('WWW-Authenticate: Basic realm="Mashape API"');
				return false;
			}
		}
	}
}
?>