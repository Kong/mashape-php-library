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
require_once(dirname(__FILE__) . "/../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../net/httpUtils.php");
require_once(dirname(__FILE__) . "/../../init/init.php");
require_once(dirname(__FILE__) . "/helpers/discoverMethods.php");
require_once(dirname(__FILE__) . "/helpers/discoverObjects.php");
require_once(dirname(__FILE__) . "/helpers/updateHtaccess.php");

define("HEADER_SERVER_KEY", "X-Mashape-Server-Key");

define("MODE", "_mode");
define("SIMPLE_MODE", "simple");

class Discover implements IMethodHandler {

	public function handle($instance, $serverKey, $parameters, $httpRequestMethod) {
		// Validate HTTP Verb
		if (strtolower($httpRequestMethod) != "get") {
			throw new MashapeException(EXCEPTION_INVALID_HTTPMETHOD, EXCEPTION_INVALID_HTTPMETHOD_CODE);
		}

		// Validate request
		if ($this->validateRequest($serverKey) == false) {
			throw new MashapeException(EXCEPTION_AUTH_INVALID_SERVERKEY, EXCEPTION_AUTH_INVALID_SERVERKEY_CODE);
		}

		$resultXml = "<?xml version=\"1.0\" ?>\n";

		$fileParts = Explode('/', $_SERVER["PHP_SELF"]);
		$scriptName = $fileParts[count($fileParts) - 1];

		$baseUrl = Explode("/" . $scriptName, $this->curPageURL());
		$resultXml .= "<api baseUrl=\"" . $baseUrl[0]  . "\" " . $this->getSimpleInfo() . ">\n";

		$mode = (isset($parameters[MODE])) ? $parameters[MODE] : null;

		$configuration = RESTConfigurationLoader::reloadConfiguration($serverKey);
		if ($mode == null || $mode != SIMPLE_MODE) {
			$objectsFound = array();
			$objectsToCreate = array();

			$methods = discoverMethods($instance, $configuration, $objectsFound, $objectsToCreate, $scriptName);
			$objects = discoverObjects($configuration, $objectsFound);
			$resultXml .= $methods . $objects . generateObjects($objectsToCreate);

			// Update the .htaccess file with the new route settings
			updateHtaccess($instance);

		}
		$resultXml .= "</api>";

		return $resultXml;
	}

	private function getSimpleInfo() {
		$libraryLanguage = "language=\"" . LIBRARY_LANGUAGE . "\"";
		$libraryVersion = " version=\"" . LIBRARY_VERSION . "\"";

		return $libraryLanguage . $libraryVersion;
	}

	private function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	private function validateRequest($serverKey) {
		// If the request comes from the local computer, then don't require authorization,
		// otherwise check the headers
		if (HttpUtils::isLocal()) {
			return true;
		} else {
			$providedServerkey = HttpUtils::getHeader(HEADER_SERVER_KEY);
			if (empty($serverKey)) {
				throw new MashapeException(EXCEPTION_EMPTY_SERVERKEY, EXCEPTION_XML_CODE);
			}
			if ($providedServerkey != null && md5($serverKey) == $providedServerkey) {
				return true;
			}
			return false;
		}
	}
}
