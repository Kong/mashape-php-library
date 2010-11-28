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

require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");
require_once(dirname(__FILE__) . "/validateParameters.php");
require_once(dirname(__FILE__) . "/serializeMethodResult.php");

function doCall($method, $parameters, $instance) {
	$hasRequiredParams = validateCallParameters($method, $parameters, $instance);
	
	$reflectedClass = new ReflectionClass(get_class($instance));
	$reflectedMethod = $reflectedClass->getMethod($method->getName());
	$result;
	if (!$hasRequiredParams) {
		$result = $reflectedMethod->invoke($instance);
	} else {
		$result = $reflectedMethod->invokeArgs($instance, $parameters);
	}

	$resultJson = '{';
	$resultJson .= '"version":"' . LIBRARY_VERSION . '",';

	//Print custom errors
	$reflectedErrorMethod = $reflectedClass->getMethod("getErrors");
	$reflectedErrors = $reflectedErrorMethod->invoke($instance);

	$resultJson .= '"errors":[';

	if (!empty($reflectedErrors)) {
		foreach ($reflectedErrors as $reflectedError) {
			$reflectedErrorClass = new ReflectionClass(get_class($reflectedError));
			$code = $reflectedErrorClass->getMethod("getCode")->invoke($reflectedError);
			$message = $reflectedErrorClass->getMethod("getMessage")->invoke($reflectedError);
			$resultJson .= '{"code":' . JsonUtils::encodeToJson($code) . ',"message":' . JsonUtils::encodeToJson($message) . '},';
		}
		$resultJson = JsonUtils::removeLastChar($reflectedErrors, $resultJson);
	}

	$resultJson .= ']';
	$resultJson .= ',"result":';
	$resultJson .= serializeMethodResult($method, $result, $instance);
	$resultJson .= '}';
	return $resultJson;
}

?>