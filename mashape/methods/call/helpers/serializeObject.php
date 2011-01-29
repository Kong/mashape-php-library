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

require_once(dirname(__FILE__) . "/../../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../../../configuration/restConfigurationLoader.php");
require_once(dirname(__FILE__) . "/../../../json/jsonUtils.php");

function serializeObject($result, $instance, $isSimpleResult, $serverKey) {
	$json = "";
	if ($isSimpleResult) {
		// It's a simple result, just serialize it
		$json = JsonUtils::encodeToJson($result);
	} else {
		// It's a custom object, let's serialize recursively every field
		$className = get_class($result);

		$reflectedClass = new ReflectionClass($className);
		$xmlObject = RESTConfigurationLoader::getObject($className, $serverKey);

		if (empty($xmlObject)) {
			throw new MashapeException(sprintf(EXCEPTION_UNKNOWN_OBJECT, $className), EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
		}

		// Start element
		$json .= "{";

		// Serialize fields
		$fields = $xmlObject->getFields();

		for ($i=0;$i<count($fields);$i++) {
			$field = $fields[$i];

			$fieldName = $field->getName();
			$fieldMethod = $field->getMethod();
			$fieldValue = null;
			if (empty($fieldMethod)) {
				$fieldValue = $reflectedClass->getProperty($fieldName)->getValue($result);
			} else {
				$fieldValue = $reflectedClass->getMethod($fieldMethod)->invoke($result);
			}

			if ($fieldValue == null && $field->isOptional()) {
				// Don't serialize the field
				continue;
			}

			$json .= '"' . $fieldName . '":';
			if ($fieldValue == null) {
				$json .= JsonUtils::encodeToJson($fieldValue);
			} else {

				$isSimpleField = isSimpleField($field);

				if ($field->isArray()) {
					$json .= "[";
					if (is_array($fieldValue)) {

						for ($t=0;$t<count($fieldValue);$t++) {
							$json .= serializeObject($fieldValue[$t], $instance, $isSimpleField, $serverKey) . ",";
						}
						$json = JsonUtils::removeLastChar($fieldValue, $json);
					} else {
						// The result it's not an array although it was described IT WAS an array
						throw new MashapeException(sprintf(EXCEPTION_EXPECTED_ARRAY_RESULT, $fieldName, $className), EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
					}
					$json .= "]";
				} else {
					if (is_array($fieldValue)) {
						// The result it's an array although it was described IT WAS NOT an array
						throw new MashapeException(sprintf(EXCEPTION_UNEXPECTED_ARRAY_RESULT, $fieldName, $className), EXCEPTION_GENERIC_LIBRARY_ERROR_CODE);
					} else {
						$json .= serializeObject($fieldValue, $instance, $isSimpleField, $serverKey);
					}
				}
			}
			$json .= ",";
		}
		$json = JsonUtils::removeLastChar($fields, $json);
		// Close element
		$json .= "}";
	}
	return $json;
}

function isSimpleField($field) {
	$object = $field->getObject();
	return empty($object);
}

?>