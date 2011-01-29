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

require_once(dirname(__FILE__) . "/../settings.php");
require_once(dirname(__FILE__) . "/../init/init.php");
require_once(dirname(__FILE__) . "/../xml/xmlParser.php");
require_once(dirname(__FILE__) . "/../xml/xmlParserUtils.php");
require_once(dirname(__FILE__) . "/helpers/loadMethods.php");
require_once(dirname(__FILE__) . "/helpers/loadObjects.php");
require_once(dirname(__FILE__) . "/restConfiguration.php");

class RESTConfigurationLoader {

	private static function getSessionVarName($serverKey) {
		return SESSION_VARNAME . $serverKey;
	}
	
	public static function reloadConfiguration($serverKey, $path=CONFIGURATION_FILEPATH) {
		$sessionVarName = self::getSessionVarName($serverKey);
		$_SESSION[$sessionVarName] = null;
		return self::loadConfiguration($serverKey, $path);
	}

	public static function loadConfiguration($serverKey, $path=CONFIGURATION_FILEPATH) {
		$sessionVarName = self::getSessionVarName($serverKey);
		$configuration = null;
		if (isset($_SESSION[$sessionVarName]) && empty($_SESSION[$sessionVarName]) == false) {
			$configuration = unserialize($_SESSION[$sessionVarName]);
		} else {
			$configuration = self::init($path);
			$_SESSION[$sessionVarName] = serialize($configuration);
		}
		return $configuration;
	}

	public static function getMethod($methodName, $serverKey) {
		$methods = self::loadConfiguration($serverKey)->getMethods();
		foreach ($methods as $method) {
			if ($method->getName() == $methodName) {
				return $method;
			}
		}
		return null;
	}

	public static function getObject($className, $serverKey) {
		$objects = self::loadConfiguration($serverKey)->getObjects();
		foreach ($objects as $object) {
			if ($object->getClassName() == $className) {
				return $object;
			}
		}
		return null;
	}

	private static function init($path) {
		$xmlParser = self::getXmlDoc($path);
		
		// Load Methods
		$methods = loadMethodsFromXML($xmlParser);

		// Load Objects
		$objects = loadObjectsFromXML($xmlParser);

		$result = new RESTConfiguration();
		$result->setMethods($methods);
		$result->setObjects($objects);
		return $result;
	}

	private static function getXmlDoc($path) {
		if (file_exists($path)) {
			$xml = file_get_contents($path);
			$xmlParser = new XMLParser($xml);
			$xmlParser->Parse();
			return $xmlParser;
		} else {
			throw new MashapeException(sprintf(EXCEPTION_CONFIGURATION_FILE_NOTFOUND, $path), EXCEPTION_XML_CODE);
		}
	}

}

?>