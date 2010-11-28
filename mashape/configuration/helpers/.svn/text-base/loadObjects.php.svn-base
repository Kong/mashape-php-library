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

require_once(dirname(__FILE__) . "/../../exceptions/mashapeException.php");
require_once(dirname(__FILE__) . "/../restField.php");
require_once(dirname(__FILE__) . "/../restObject.php");

define("XML_OBJECT", "object");
define("XML_OBJECT_CLASS", "class");

define("XML_FIELD", "field");
define("XML_FIELD_OBJECT", "object");
define("XML_FIELD_METHOD", "method");
define("XML_FIELD_ARRAY", "array");
define("XML_FIELD_OPTIONAL", "optional");

function loadObjectsFromXML($xmlDocument) {
	$objects = array();

	$xmlObjects = $xmlDocument->getElementsByTagName(XML_OBJECT);
	foreach($xmlObjects as $xmlObject)
	{
		$className = $xmlObject->getAttribute(XML_OBJECT_CLASS);
		if (empty($className)) {
			throw new MashapeException(EXCEPTION_OBJECT_EMPTY_CLASS, EXCEPTION_XML_CODE);
		} else if (existClassName($objects, $className)) {
			throw new MashapeException(sprintf(EXCEPTION_OBJECT_DUPLICATE_CLASS, $className), EXCEPTION_XML_CODE);
		}

		//Get fields
		$fields = array();
		$xmlFields = $xmlObject->getElementsByTagName(XML_FIELD);
		foreach($xmlFields as $xmlField)
		{
			$field_name = $xmlField->textContent;
			if (empty($field_name)) {
				throw new MashapeException(EXCEPTION_FIELD_EMPTY_NAME, EXCEPTION_XML_CODE);
			} else if (existFieldName($fields, $field_name)) {
				throw new MashapeException(sprintf(EXCEPTION_FIELD_NAME_DUPLICATE, $field_name, $className), EXCEPTION_XML_CODE);
			}

			$field_object = $xmlField->getAttribute(XML_FIELD_OBJECT);
			$field_method = $xmlField->getAttribute(XML_FIELD_METHOD);

			$field_array = $xmlField->getAttribute(XML_FIELD_ARRAY);
			if ($field_array != null && strtolower($field_array) == "true") {
				$field_array = true;
			} else {
				$field_array = false;
			}

			$field_optional = $xmlField->getAttribute(XML_FIELD_OPTIONAL);
			if ($field_optional != null && strtolower($field_optional) == "true") {
				$field_optional = true;
			} else {
				$field_optional = false;
			}

			$field = new RESTField();
			$field->setName($field_name);
			$field->setObject($field_object);
			$field->setMethod($field_method);
			$field->setArray($field_array);
			$field->setOptional($field_optional);
			array_push($fields, $field);
		}

		$object = new RESTObject();
		$object->setClassName($className);
		$object->setFields($fields);
		array_push($objects, $object);
	}
	return $objects;
}

function existClassName($objects, $className) {
	foreach ($objects as $object) {
		if ($object->getClassName() == $className) {
			return true;
		}
	}
	return false;
}

function existFieldName($fields, $fieldName) {
	foreach ($fields as $field) {
		if ($field->getName() == $fieldName) {
			return true;
		}
	}
	return false;
}

?>