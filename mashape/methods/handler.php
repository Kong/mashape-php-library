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

require_once(dirname(__FILE__) . "/../init/init.php");
require_once(dirname(__FILE__) . "/../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/../net/httpUtils.php");
require_once(dirname(__FILE__) . "/discover/discover.php");
require_once(dirname(__FILE__) . "/call/call.php");

define("OPERATION", "_op");
define("CALLBACK", "callback");

class MashapeHandler {

	private static function setUnauthorizedResponse() {
		header("HTTP/1.0 401 Unauthorized");
		header('WWW-Authenticate: Mashape realm="Mashape API authentication"');
	}

	private static function getAllParams($source) {
		$keys = array_keys($source);
		$result = array();
		foreach ($keys as $key) {
			$result[$key] = $source[$key];
		}
		return $result;
	}

	private static function validateCallback($callback) {
		if (preg_match("/^(\w\.?_?)+$/", $callback)) {
			return true;
		} else {
			return false;
		}
	}

	public static function handleAPI($instance, $serverKey) {
		header("Content-type: application/json");
		try {
			if ($instance == null) {
				throw new MashapeException(EXCEPTION_INSTANCE_NULL, EXCEPTION_SYSTEM_ERROR_CODE);
			}
			$requestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? strtolower($_SERVER['REQUEST_METHOD']) : null;
			$params;
			if ($requestMethod == 'post') {
				$params = self::getAllParams($_POST);
			} else if ($requestMethod == 'get') {
				$params = self::getAllParams($_GET);
			} else if ($requestMethod == 'put' || $requestMethod == 'delete') {
				$params = HttpUtils::parseQueryString(file_get_contents("php://input"));
			} else {
				throw new MashapeException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
			}

			$operation = (isset($params[OPERATION])) ? $params[OPERATION] : null;
			unset($params[OPERATION]); // remove the operation parameter

			if (empty($operation)) {
				$operation = "call";
			}
			if ($operation != null) {
				$result;
				switch (strtolower($operation)) {
					case "discover":
						header("Content-type: application/xml");
						$discover = new Discover();
						$result = $discover->handle($instance, $serverKey, $params, $requestMethod);
						break;
					case "call":
						$call = new Call();
						$result = $call->handle($instance, $serverKey, $params, $requestMethod);
						break;
					default:
						throw new MashapeException(EXCEPTION_NOTSUPPORTED_OPERATION, EXCEPTION_NOTSUPPORTED_OPERATION_CODE);
				}

				$jsonpCallback = (isset($params[CALLBACK])) ? $params[CALLBACK] : null;
				if (empty($jsonpCallback)) {
					// Print the output
					echo $result;
				} else {
					if (self::validateCallback($jsonpCallback)) {
						echo $jsonpCallback . '(' . $result . ')';
					} else {
						throw new MashapeException(EXCEPTION_INVALID_CALLBACK, EXCEPTION_SYSTEM_ERROR_CODE);
					}
				}

			} else {
				// Operation not supported
				throw new MashapeException(EXCEPTION_NOTSUPPORTED_OPERATION, EXCEPTION_NOTSUPPORTED_OPERATION_CODE);
			}

		} catch (Exception $e) {
			//If it's an ApizatorException then print the specific code
			if ($e instanceof MashapeException) {
				header("Content-type: application/json");
				$code = $e->getCode();
				switch ($code) {
					case EXCEPTION_XML_CODE:
						header("HTTP/1.0 500 Internal Server Error");
						break;
					case EXCEPTION_INVALID_HTTPMETHOD_CODE:
						header("HTTP/1.0 405 Method Not Allowed");
						break;
					case EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE:
						header("HTTP/1.0 405 Method Not Allowed");
						break;
					case EXCEPTION_NOTSUPPORTED_OPERATION_CODE:
						header("HTTP/1.0 501 Not Implemented");
						break;
					case EXCEPTION_METHOD_NOTFOUND_CODE:
						header("HTTP/1.0 200 OK");
						break;
					case EXCEPTION_AUTH_INVALID_CODE:
						self::setUnauthorizedResponse();
						break;
					case EXCEPTION_AUTH_INVALID_SERVERKEY_CODE:
						self::setUnauthorizedResponse();
						break;
					case EXCEPTION_REQUIRED_PARAMETERS_CODE:
						header("HTTP/1.0 200 OK");
						break;
					case EXCEPTION_GENERIC_LIBRARY_ERROR_CODE:
						header("HTTP/1.0 500 Internal Server Error");
						break;
					case EXCEPTION_INVALID_APIKEY_CODE:
						self::setUnauthorizedResponse();
						break;
					case EXCEPTION_EXCEEDED_LIMIT_CODE:
						self::setUnauthorizedResponse();
						break;
					case EXCEPTION_SYSTEM_ERROR_CODE:
						header("HTTP/1.0 500 Internal Server Error");
						break;
				}
				echo JsonUtils::serializeError($e->getMessage(), $code);
			} else {
				//Otherwise print a "generic exception" code
				header("HTTP/1.0 500 Internal Server Error");
				echo JsonUtils::serializeError($e->getMessage(), EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
			}
		}
	}
}
