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

		$resultJson = "{";

		$mode = (isset($parameters[MODE])) ? $parameters[MODE] : null;
		
		$configuration = RESTConfigurationLoader::reloadConfiguration($serverKey);
		if ($mode == null || $mode != SIMPLE_MODE) {
			$objectsFound = array();
			$methods = discoverMethods($instance, $configuration, $objectsFound);
			$objects = discoverObjects($configuration, $objectsFound);
			$resultJson .= $methods . "," . $objects . "," . $this->getSimpleInfo();
			
			// Update the .htaccess file with the new route settings
			updateHtaccess($instance);
			
		} else {
			$resultJson .= $this->getSimpleInfo();
		}
		$resultJson .= "}";

		return $resultJson;
	}
	
	private function getSimpleInfo() {
		$libraryVersion = '"version":"' . LIBRARY_VERSION . '"';
		$libraryLanguage = '"language":"' . LIBRARY_LANGUAGE . '"';
		
		return $libraryLanguage . "," . $libraryVersion;
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
